<?php

namespace SejourBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EditActiviteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', null, array('label'=>'Nom de l\'activité'))
				->add('categorie', EntityType::class, array('class' => 'SejourBundle:CateActi',
														'choice_label' => 'Categorie',
														'multiple'	=> false,
														'expanded' => false,
														'label' => 'Catégorie d\'activité'
														))
				->add('description', CKEditorType::class, array('config' => array('uiColor' => '#ffffff'), 'label'=>'Description du déroulement', 'config_name'=>'bbcode'))
				->add('materiel', null, array('label'=>'Matéreil necessaire'))
				->add('nbEnfantMin', null, array('label'=>'Nombre d\'enfants minimum'))
				->add('nbEnfantMax', null, array('label'=>'Nombre d\'enfants maximum'))
				->add('nbAnim', null, array('label'=>'Nombre d\'animateur prévisionnel'))
				->add('Modifier l\'activite !',      SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SejourBundle\Entity\Activite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sejourbundle_editactivite';
    }


}
