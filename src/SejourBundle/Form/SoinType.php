<?php

namespace SejourBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SoinType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$ListeEnfant=$options['ListeEnfant'];
	$ListeJour=$options['ListeJour'];
        $builder->add('objet')
				->add('temperature', RangeType::class,  array('attr' => array('min' => 35, 'max'=>42, 'step'=>'.1', 'onchange'=>"updateTextInput(this.value);"), 'data'=> 37))
				->add('soinsDispenses')
				->add('observations')	
				->add('heure')	
				->add('jour', EntityType::class, array(
    'class' => 'SejourBundle:Jour',
    'choices' => $ListeJour,
	'choice_label' => 'DateFormulaire',
))
				->add('Enregistrer',      SubmitType::class)	
				->add('enfant', EntityType::class, array(
    'class' => 'SejourBundle:Enfant',
    'choices' => $ListeEnfant,
	'choice_label' => 'prenomnom',
));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SejourBundle\Entity\Soin'
        ))
		->setRequired(['ListeJour'])
		->setRequired(['ListeEnfant']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_soin';
    }


}
