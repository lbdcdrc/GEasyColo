<?php

namespace SejourBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SejourBundle\Entity\Sejour;
use SejourBundle\Form\Type\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EnfantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$mode=$options['edit'];
		
		if ($mode)
		{
		$builder	->add('nom',null, array('disabled'=>true))
					->add('prenom',null, array('disabled'=>true))
					->add('age')
					->add('infos')
					->add('regimes', null, array('label'=>'Régimes Alimentaires :'))
					->add('chambre')
		->add('Enregistrer les modifications',      SubmitType::class);
		}
		else
		{
		$builder	->add('nom')
					->add('prenom')
					->add('age')
					->add('infos')
					->add('regimes', null, array('label'=>'Régimes Alimentaires :'))
					->add('chambre')
					->add('image',  ImageType::class, array('required'=>false))
					->add('Creer l\'enfant',      SubmitType::class);			
		}
		
       
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'SejourBundle\Entity\Enfant'))
      
		->setRequired(['edit']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_enfant';
    }


}
