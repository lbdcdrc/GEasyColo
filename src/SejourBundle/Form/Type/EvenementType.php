<?php

namespace SejourBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TimeType;


class EvenementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$Sejour=$options['Sejour'];
        $builder->add('heureDebut', TimeType::class, array( 'widget' => 'single_text',
                    'label' => 'Heure de début :',
                    'required' => true,
                    'html5' => true,
                    'attr' => array(
						'class' => 'timepicker',
						'data-toggle' => 'timepicker',
                    ),))
				->add('heureFin', TimeType::class, array( 'widget' => 'single_text',
                    'label' => 'Heure de fin :',
                    'required' => true,
                    'html5' => true,
                    'attr' => array(
						'class' => 'timepicker',
						'data-toggle' => 'timepicker',
                    ),))
				->add('activite', EntityType::class, array(
								'class' => 'SejourBundle:Activite',
								'choice_label' => 'Nom',
								'expanded' => false,
								'required' => false,
								'query_builder' => function (EntityRepository $er) use ($Sejour){
								return $er->createQueryBuilder('u')
									->addSelect('s')
									->join('u.sejour', 's')
									->where('s.id = :moment')
									->setParameter('moment', $Sejour)
									->orderBy('u.id', 'ASC');
					}))
				->add('NbPlaces', null, array('label'=>"Nombre de places", 'attr' => array('min' => 0)))
				->add('Ajouter l\'activite !',      SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SejourBundle\Entity\Evenement'
        ))
			->setRequired(['Sejour']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_evenement';
    }


}
