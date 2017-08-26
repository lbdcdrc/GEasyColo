<?php
 
// src/UserBundle/Form/ProfileType.php
 
namespace UserBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
 
class ProfileType extends AbstractType
 
{
   public function buildForm(FormBuilderInterface $builder, array $options)
 
   {

	   $builder	->add('telephone', null, array('label'=>'Numéro de portable', 'attr'=>array('placeholder'=>'+33601020304')))
				->add('adresse1', null, array('label'=>'Champ d\'adresse 1 :', 'attr'=>array('placeholder'=>'Numéro et nom de rue')))
				->add('adresse2', null, array('label'=>'Champ d\'adresse 2 :', 'required'=>false, 'attr'=>array('placeholder'=>'Complément d\'adresse')))
				->add('codepostal', null, array('label'=>'Code Postal'))
				->add('ville')
				->add('diplome', EntityType::class, array('class' => 'UserBundle:Diplome','choice_label' => 'nom',))
				->add('psc1', CheckboxType::class, array('label'    => 'PSC1', 'required'=>false))
				->add('sb', CheckboxType::class, array('label'    => 'Surveillant de Baignade', 'required'=>false))
				->add('presentation', CKEditorType::class, array('config' => array('uiColor' => '#ffffff'), 'label'=>'Ma présentation', 'config_name'=>'bbcode'));				
		if (isset($options['attr']['edit']) && ($options['attr']['edit'] === true))
		{
			$builder->add('nom',null, array('label'=>"Nom de famille"))
					->add('prenom',null, array('label'=>"Prénom"))
					->add('naissance', BirthdayType::class, array('label'=>"Date de naissance"))
					->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
					->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'));

		}
		else
		{
		$builder->add('nom',null, array('label'=>"Nom de famille", 'disabled'=>true))
				->add('prenom',null, array('label'=>"Prénom", 'disabled'=>true))
				->add('naissance', BirthdayType::class, array('label'=>"Date de naissance", 'disabled'=>true))
				->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'disabled' => true))
				->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'disabled' => true));
		}	
   }
 
   public function getParent()
 
   {
       return 'FOS\UserBundle\Form\Type\ProfileFormType';
   }
 
   public function getBlockPrefix()
 
   {
       return 'fos_user_profile_edit';
   }
 
   public function getName()
 
   {
       return $this->getBlockPrefix();
   }
 
}
