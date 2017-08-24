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


class AffecterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$jour=$options['jour'];
        $builder
		->add('Matin1', EntityType::class, array(
		'label' => 'Matin, Premier créneau :',
		'class'=>'SejourBundle:Evenement',
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '1')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->andwhere('u.estlie = false')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false))
		->add('Matin2', EntityType::class, array(
		'label' => 'Matin, Deuxième créneau :',
		'class'=>'SejourBundle:Evenement',
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '2')
			->andwhere('u.jour = :jourId')
			->andwhere('u.estlie = false')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false))
			->add('AM', EntityType::class, array(
		'label' => 'Après Midi :',
		'class'=>'SejourBundle:Evenement',
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '3')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false))
			->add('Jour12', EntityType::class, array(
		'label' => 'Journée plus matinée du lendemain :',
		'class'=>'SejourBundle:Evenement',
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '4')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false))
			->add('Journee', EntityType::class, array(
		'label' => 'Journée Entière :',
		'class'=>'SejourBundle:Evenement',
		'query_builder' => function (EntityRepository $er) use ($jour){
        return $er->createQueryBuilder('u')
			->where('u.Moment = :moment')
			->setParameter('moment', '5')
			->andwhere('u.jour = :jourId')
			->andwhere('u.NbInscrits < u.NbPlaces')
			->setParameter('jourId', $jour)
            ->orderBy('u.id', 'ASC');
    },
    'choice_label' => 'activite.nom', 'expanded' => true, 'required' => false))
		->add('Inscrire l\'enfant',      SubmitType::class);
    }

	public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setRequired(['jour']);
}
}
