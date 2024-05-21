<?php


namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormType extends AbstractType
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email address',
                'label_attr' => ['class' => 'sr-only'],
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => 'Email address',
                    'id' => 'header-signin-email'
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'label_attr' => ['class' => 'sr-only'],
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => 'Password',
                    'id' => 'header-signin-password'
                ],
            ])
            ->add('remember_me', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false, // On ne veut pas de label automatique
                'attr' => ['class' => 'input-check__input', 'id' => 'signin-remember'],
            ])
            ->add('_csrf_token', HiddenType::class, [
                'data' => $this->csrfTokenManager->getToken('authenticate')->getValue(),
            ])->add('captcha', Recaptcha3Type::class, [
                'action_name' => 'login',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
