<?php

namespace SejourBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\DateTime;

class SejourType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
		
		->add('dateDebut', DateType::class, array(
                
                    'widget' => 'single_text',
                    'label' => 'Date de début :',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                    'html5' => true,
                    'attr' => array(
						'class' => 'datepicker',
						'data-toggle' =>"datepicker",
                        'placeholder' => 'jj/mm/aaaa ex: 25/12/2017',
                    ),
                    'constraints' => [new DateTime()],
                ))
		->add('dateFin', DateType::class, array(
                
                    'widget' => 'single_text',
                    'label' => 'Date de fin :',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                    'html5' => true,
                    'attr' => array(
						'class' => 'datepicker',
						'data-toggle' =>"datepicker",
                        'placeholder' => 'jj/mm/aaaa ex: 31/01/2018',
                    ),
                    'constraints' => [new DateTime()],
                ))
		->add('nomThema',null,array('label' => 'Nom du Séjour :'))
		->add('Creer le sejour !',      SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SejourBundle\Entity\Sejour'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_sejour';
    }


}
