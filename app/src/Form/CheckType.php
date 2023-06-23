<?php
    
    namespace App\Form;
    
    use App\Entity\Check;
    use App\Entity\WebsiteContactSubject;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class CheckType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);
            
            $builder
                ;
        }
    
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults(
                [
                    'data_class' => Check::class,
                ]
            );
        }
    }
