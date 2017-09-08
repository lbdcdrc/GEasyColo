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


class EvenementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Moment', EntityType::class, array(
								'class' => 'SejourBundle:MomentActivite',
								'choice_label' => 'nomMoment',
								'expanded' => false,
								'required' => false,))
				->add('activite', EntityType::class, array(
								'class' => 'SejourBundle:Activite',
								'choice_label' => 'Nom',
								'expanded' => false,
								'required' => false,
								'query_builder' => function (EntityRepository $er){
								return $er->createQueryBuilder('u')
									->addSelect('s')
									->join('u.sejour', 's')
									->where('s.id = :moment')
									->setParameter('moment', '4')
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
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_evenement';
    }


}
