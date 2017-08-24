<?php

namespace SejourBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EvenementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Moment', ChoiceType::class, array('choices' => array('Matin Premier Creneau' =>1, 'Matin Deuxième Creneau' => 2, 'Après Midi' => 3, 'Journée et demi'=>4, 'Journée'=>5), 'expanded' => true, 'multiple' => false))
				->add('activite', EntityType::class, array(
			// query choices from this entity
			'class' => 'SejourBundle:Activite',

			// use the User.username property as the visible option string
			'choice_label' => 'Nom',

			// used to render a select box, check boxes or radios
			// 'multiple' => true,
			// 'expanded' => true,
		))
				->add('NbPlaces', null, array('label'=>"Nombre de places"))
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
