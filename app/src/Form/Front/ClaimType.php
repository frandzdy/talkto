<?php
    
    namespace App\Form\Front;
    
    use App\Entity\Claim;
    use App\Entity\WebsiteContactSubject;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class ClaimType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            parent::buildForm($builder, $options);
            
            $builder
                ;
        }
    
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults(
                [
                    'data_class' => Claim::class,
                ]
            );
        }
    }
