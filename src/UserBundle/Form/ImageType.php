<?php
// src/UserBundle/Form/ImageType.php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ImageType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('file', FileType::class, array('label'=>'Choisir mon avatar'))
	  ->add('Enregistrer mon avatar',      SubmitType::class);
    
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'SejourBundle\Entity\Image'
    ));
  }
}