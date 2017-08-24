<?php
 
// src/UserBundle/Form/RegistrationType.php
 
namespace UserBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use SejourBundle\Form\Type\ImageType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
 
class RegistrationType extends AbstractType
 
{
   public function buildForm(FormBuilderInterface $builder, array $options)
 
   {
	   $builder->add(
					$builder->create('step1', FormType::class, array('label'=> 'État Civil :', 'inherit_data' => true))
							->add('nom',null, array('label'=>"Nom de famille"))
							->add('prenom',null, array('label'=>"Prénom"))
							->add('image',  ImageType::class, array('label'=>'Photo de profil', 'required'=>false))
							->add('naissance', BirthdayType::class, array('label'=>"Date de naissance"))
							->add('telephone', null, array('label'=>'Numéro de portable', 'attr'=>array('placeholder'=>'+33601020304')))
							->add('adresse1', null, array('label'=>'Champ d\'adresse 1 :', 'attr'=>array('placeholder'=>'Numéro et nom de rue')))
							->add('adresse2', null, array('label'=>'Champ d\'adresse 2 :', 'required'=>false, 'attr'=>array('placeholder'=>'Complément d\'adresse')))
							->add('codepostal', null, array('label'=>'Code Postal'))
							->add('ville'));							
							
	   
	   $builder->add(
					$builder->create('step2', FormType::class, array('label'=>'Qualifications :', 'inherit_data' => true))
							->add('diplome', ChoiceType::class, array('label'=>'Diplôme', 'choices' => array(
														'Sans BAFA'=>0,
														'BAFA Stage 1 fait'=>1,
														'BAFA Stage pratique fait'=>2,
														'BAFA Stage 2 fait'=>3,
														'BAFA Diplômé'=>4,
														'Équivalence BAFA'=>5,
														'BAFD Stage 1 fait'=>6,
														'BAFD Stage pratique 1 fait'=>7,
														'BAFD Stage 2 fait'=>8,
														'BAFD Stage pratique 2 fait'=>9,
														'BAFD Diplômé'=>10,
														'Équivalence BAFD'=>11
														)))
							->add('psc1', CheckboxType::class, array('label'    => 'PSC1', 'required'=>false))
							->add('sb', CheckboxType::class, array('label'    => 'Surveillant de Baignade', 'required'=>false)));
		$builder->add('presentation', CKEditorType::class, array('config' => array('uiColor' => '#ffffff'), 'label'=>'Ma présentation', 'config_name'=>'bbcode'));

   }
 
   public function getParent()
 
   {
       return 'FOS\UserBundle\Form\Type\RegistrationFormType';
   }
 
   public function getBlockPrefix()
 
   {
       return 'app_user_registration';
   }
 
   public function getName()
 
   {
       return $this->getBlockPrefix();
   }
 
}
