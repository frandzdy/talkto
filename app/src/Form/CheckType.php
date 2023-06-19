<?php
    
    namespace App\Form;
    
    use App\Entity\Check;
    use App\Entity\ContactSubject;
    use App\Entity\WebsiteContactSubject;
    use App\Model\ContactModel;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
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
