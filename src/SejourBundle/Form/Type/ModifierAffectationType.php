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


class ModifierAffectationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$jour=$options['jour'];
		
	if($options['Matin1']==Null)
	{
		$builder
		->add('Matin1', EntityType::class, array(
		'label' => 'Matin, Premier créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin1'],
		'query_builder' => function (EntityRepository $er) use ($jour)
		{
			return $er->createQueryBuilder('u')
				->where('u.Moment = :moment')
				->setParameter('moment', '1')
				->andwhere('u.jour = :jourId')
				->andwhere('u.estlie = false')
				->andwhere('u.NbInscrits < u.NbPlaces')
				->setParameter('jourId', $jour)
				->orderBy('u.id', 'ASC');
		},
		'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));	
		
		
		if($options['Matin2'] && $options['Matin2']->getNbInscrits() >= $options['Matin2']->getNbPlaces() )
		{
		$builder
		->add('Matin2', EntityType::class, array(
		'label' => 'Matin, Deuxième créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin2'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '2')
			->andwhere('u.jour = :jourId')
			->andwhere('u.estlie = false')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));
		}
		else
		{
		$builder
		->add('Matin2', EntityType::class, array(
		'label' => 'Matin, Deuxième créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin2'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '2')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->andwhere('u.estlie = false')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));		
		}
	
	}
	
	elseif($options['Matin1']->getEstlie()===false)
	{
		if($options['Matin1'] && $options['Matin1']->getNbInscrits() >= $options['Matin1']->getNbPlaces() )
		{
        $builder
		->add('Matin1', EntityType::class, array(
		'label' => 'Matin, Premier créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin1'],
		'query_builder' => function (EntityRepository $er) use ($jour)
		{
			return $er->createQueryBuilder('u')
				->where('u.Moment = :moment')
				->setParameter('moment', '1')
				->andwhere('u.jour = :jourId')
				->andwhere('u.estlie = false')
				->setParameter('jourId', $jour)
				->orderBy('u.id', 'ASC');
		},
		'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));
		}
		else
		{
		$builder
		->add('Matin1', EntityType::class, array(
		'label' => 'Matin, Premier créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin1'],
		'query_builder' => function (EntityRepository $er) use ($jour)
		{
			return $er->createQueryBuilder('u')
				->where('u.Moment = :moment')
				->setParameter('moment', '1')
				->andwhere('u.jour = :jourId')
				->andwhere('u.estlie = false')
				->andwhere('u.NbInscrits < u.NbPlaces')
				->setParameter('jourId', $jour)
				->orderBy('u.id', 'ASC');
		},
		'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));	
		}
		
		
		
		
		if($options['Matin2'] && $options['Matin2']->getNbInscrits() >= $options['Matin2']->getNbPlaces() )
		{
		$builder
		->add('Matin2', EntityType::class, array(
		'label' => 'Matin, Deuxième créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin2'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '2')
			->andwhere('u.jour = :jourId')
			->andwhere('u.estlie = false')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));
		}
		else
		{
		$builder
		->add('Matin2', EntityType::class, array(
		'label' => 'Matin, Deuxième créneau :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Matin2'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '2')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->andwhere('u.estlie = false')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));		
		}
	}
		if($options['AM'] && $options['AM']->getNbInscrits() >= $options['AM']->getNbPlaces() )
		{
		$builder
			->add('AM', EntityType::class, array(
		'label' => 'Après Midi :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['AM'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '3')
			->andwhere('u.jour = :jourId')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));			
		}
		else
		{
		$builder
			->add('AM', EntityType::class, array(
		'label' => 'Après Midi :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['AM'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '3')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));
		}
		if($options['Jour12'] && $options['Jour12']->getNbInscrits() >= $options['Jour12']->getNbPlaces() )
		{
		$builder
		->add('Jour12', EntityType::class, array(
		'label' => 'Journée plus matinée du lendemain :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Jour12'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '4')
			->andwhere('u.jour = :jourId')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));
		}
		else
		{
		$builder
		->add('Jour12', EntityType::class, array(
		'label' => 'Journée plus matinée du lendemain :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Jour12'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '4')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));			
		}
		if($options['Journee'] && $options['Journee']->getNbInscrits() >= $options['Journee']->getNbPlaces() )
		{
		$builder
			->add('Journee', EntityType::class, array(
		'label' => 'Journée Entière :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Journee'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '5')
			->andwhere('u.jour = :jourId')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));
		}
		else
		{
		$builder
			->add('Journee', EntityType::class, array(
		'label' => 'Journée Entière :',
		'class'=>'SejourBundle:Evenement',
		'data' => $options['Journee'],
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '5')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false));			
		}
	
	
	
	
	$builder
		->add('Modifier les inscriptions',      SubmitType::class);
    }

	public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setRequired(['jour', 'Matin1', 'Matin2', 'AM', 'Jour12', 'Journee']);
}
}
