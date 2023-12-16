<?php

namespace App\Form\Back;

use App\Entity\WebsiteContent;
use App\Enum\Link;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre'
                ]
            )
            ->add(
                'subTitle',
                TextType::class,
                [
                    'label' => 'Sous titre'
                ]
            )
            ->add(
                'link',
                EnumType::class,
                [
                    'class' => Link::class,
                    'label' => 'Lien vers',
                    'choice_label' => 'label',
                    'choice_translation_domain' => 'messages'
                ]
            )
            ->add(
                'uploadedPicture',
                FileType::class,
                [
                    'label' => 'Image (SVG)',
                    'attr' =>
                        [
                            'accept' => 'image/svg+xml, image/png, image/jpg, image/jpeg',
                            'lang' => 'fr'
                        ]
                ]
            );

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $websiteContent = $event->getData();

            if (!$websiteContent->getUploadedPicture() && !$form->get('uploadedPicture')->getData()) {
                $form->get('uploadedPicture')->addError(new FormError('Information requise.'));
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => WebsiteContent::class
            ]
        );
    }
}
