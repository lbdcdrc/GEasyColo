<?php

namespace SejourBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RecruterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$listeAnim=$options['listeAnim'];
		$builder->add('user', EntityType::class, array(
						'class' => 'UserBundle:User',
						'choices' => $listeAnim,
						'choice_label'=>'PrenomNom',
						'label'=>'Animateur à recruter',
						'required'=>true,
						));
		$builder->add('role', ChoiceType::class, array(
						'choices'  => array(
							'Animateur' => 1,
							'Assistant Sanitaire' => 2,
							'Adjoint' => 3,
						), 'expanded'=>true, 'multiple'=>false, 'required'=>true, 'label'=>" "
						));
		$builder->add('Recruter l\'animateur',      SubmitType::class);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SejourBundle\Entity\AnimSejour'
        ))
		->setRequired(['listeAnim']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_animsejour';
    }



}
