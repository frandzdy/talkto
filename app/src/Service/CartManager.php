<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Enum\TransactionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Service de gestion du panier
 */
class CartManager
{
    /**
     * CartManager constructor.
     */
    public function __construct(
        protected EntityManagerInterface $em,
        protected TokenStorageInterface $token,
        protected RequestStack $requestStack
    ) {
    }

    /**
     * Retourne le panier correspondant au cookie de l'utilisateur
     */
    public function getCart(): ?array
    {
        return $this->session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'paymentIntentId' => null,
                'transactionId' => null
            ]
        );
    }
    public function saveCart($cart): void
    {
        $this->session->set('cart', $cart);
    }
        /*if (!$cartData) {
            return $cart;
        }
        $transaction = (new Transaction())
            ->setStatus(TransactionStatus::WAITING);
        $productAllreadyInCart = [];
        foreach ($cartData as $qualificationId => $itemData) {
            $product = $this->em->getRepository(Product::class)->find($qualificationId);
            //si on a une qualif et qu'elle n'est pas dans notre tableau
            if ($product && !in_array($product->getId(), $productAllreadyInCart)) {
                $productAllreadyInCart[] = $product->getId();
                $transactionLine = (new TransactionLine())
                    ->setTransaction($transaction)
                    ->setProduct($product)
                    ->setStartDate($itemData['startDate'])
                    ->setEndDate($itemData['endDate'])
                    ->setQuantity($itemData['endDate']);
            }
        }

        return $cart;*/
    //}

    /**
     * Retourne le panier existant ou initialisé
     */
    public function getOrInitCart(): array
    {
        return $this->getCartFromCookie();
    }

    /**
     * Retourne le nombre d'article dans le panier
     */
    public function getNbItemsInCart(): int
    {
        return $this->getOrInitCart()->getItems()->count();
    }

    /**
     * Sauvegarde le panier en session
     */
    public function saveUserCart(CustomerBasket $basket): void
    {
        if (!$basket->getId()) {
            $this->em->persist($basket);
        }

        $basket->setLastUpdatedAt(new \DateTime());

        $this->em->flush();
    }

    /**
     * Retourne vrai si la qualification existe déjà dans le panier
     */
    public function containsQualification(Qualification $qualification): bool
    {
        return $this->getOrInitCart()->containsQualification($qualification->getSageCode());
    }

    /**
     * Retourne l'article du panier qui correspond à la qualification en parametre
     */
    public function getCartQualificationItem(Qualification $qualification): ?CustomerBasketItem
    {
        foreach ($this->getOrInitCart()->getItems() as $item) {
            if ($item->getQualification() === $qualification) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Gère l'ajout ou la mise à jour d'un item au panier
     */
    public function addItemToCart(CustomerBasketItem $item): CustomerBasket
    {
        $cart = $this->getOrInitCart();

        foreach ($cart->getItems() as $existingItem) {
            if ($existingItem->getQualification()->getId() == $item->getQualification()->getId()) {
                $cart->removeItem($existingItem);
                $this->em->remove($existingItem);
            }
        }

        $cart->addItem($item);

        if ($this->getUser()) {
            $this->em->persist($item);
            $this->saveUserCart($cart);
        }

        return $cart;
    }

    /**
     * Supprime l'item du panier dont la qualification a le $sageCode
     */
    public function removeItemFromCart(string $sageCode): CustomerBasket
    {
        $cart = $this->getOrInitCart();

        foreach ($cart->getItems() as $item) {
            if ($item->getQualification()->getSageCode() == $sageCode) {
                $cart->removeItem($item);
            }
        }

        if ($this->getUser()) {
            $this->saveUserCart($cart);
        }

        return $cart;
    }

    /**
     * Retourne les prix du panier
     */
    public function getCartPrices(): array
    {
        if ($this->getNbItemsInCart() === 0) {
            return $return = [
                'feesTaxes' => "0,00",
                'fees' => "0,00",
                'feesTotal' => "0,00",
                'discount' => '0,00',
                'total' => "0,00",
                'lines' => [
                    'qualifications' => "0,00",
                    'mentions' => "0,00",
                    'domains' => "0,00"
                ],
                'qualifications' => [],
                'schedule' => []
            ];
        }

        if ($this->getUser() && $this->getUser()->getCompany()->getMainAddress()) {
            return $this->sageManager->getItemPrices(
                $this->getOrInitCart()->getSageCodes(),
                $this->getUser()->getCountryCode(),
                $this->getUser()->getZipcode()
            );
        }

        return $this->sageManager->getItemPrices($this->getOrInitCart()->getSageCodes());
    }

    /**
     * Fonction de fusion entre les paniers cookie et bdd d'un utilisateur
     */
    public function mergeCookieCartWithUserCart()
    {
        if (!$this->getUser()) {
            throw new \LogicException();
        }

        $cookieCart = $this->getCartFromCookie();
        $userCart = $this->getOrInitCart();

        foreach ($cookieCart->getItems() as $item) {
            $this->em->persist($item);
            if (!$userCart->containsQualification($item->getQualification()->getSageCode())) {
                $userCart->addItem($item);
            } else {
                $oldItem = $userCart->getItemByQualificationCode($item->getQualification()->getSageCode());
                $userCart->removeItem($oldItem);
                $this->em->remove($oldItem);
                $userCart->addItem($item);
            }
        }

        $this->saveUserCart($userCart);
    }

    /**
     * Sauvegarde les informations du récapitulatif dans le panier
     */
    public function setCartAddresses(CustomerBasket $cart, $summaryData): void
    {
        if ($summaryData['sameDeliveryAddress']) {
            $cart->setDeliveryAddress($cart->getAccount()->getCompany()->getMainAddress());
        } else {
            $cart->setDeliveryAddress(
                $this->em->getRepository(CustomerCompanyAddress::class)->find($summaryData['deliveryAddress'])
            );
        }

        if ($summaryData['sameBillingAddress']) {
            $cart->setBillingAddress($cart->getAccount()->getCompany()->getMainAddress());
        } else {
            $cart->setBillingAddress(
                $this->em->getRepository(CustomerCompanyAddress::class)->find($summaryData['billingAddress'])
            );
        }
    }

    /**
     * Création de l'objet Order pour le panier
     */
    public function createCartOrder(CustomerBasket $basket, $summaryData): void
    {
        $prices = $this->sageManager->getItemPrices(
            $this->getOrInitCart()->getSageCodes(),
            $this->getUser()->getCountryCode(),
            $this->getUser()->getZipcode(),
            false
        );

        if (!$prices) {
            return;
        }

        $order = new CustomerOrder();
        $order->setAccount($basket->getAccount());
        $order->setAmount(round($prices['feesPrice'] + $prices['feesPriceTaxes'], 2));
        $order->setCustomerOrderFormNumber($summaryData['orderFormNumber'] ?? '');
        $order->setCreatedAt(new \DateTime());

        $this->em->persist($order);
        $basket->setOrder($order);

        $this->em->flush();
    }

    /**
     * Retourne le panier d'un compte
     */
    public function getOrders(CustomerAccount $account): ?array
    {
        if ($account) {
            $orders = $this->em->getRepository(CustomerOrder::class)->findBy(['account' => $account]);
            if ($orders) {
                return $orders;
            }
        }

        return null;
    }
}
