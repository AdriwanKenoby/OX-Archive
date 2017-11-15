<?php

namespace Archivage\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', TextType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if( !$user || null === $user->getId()) {
                    $form->add('password', RepeatedType::class, array(
                        'type'            => PasswordType::class,
                         'invalid_message' => 'The password fields must match.',
                         'options'         => array('required' => true),
                         'first_options'   => array('label' => 'Password'),
                         'second_options'  => array('label' => 'Repeat password'),
                     ));
                }
            })
            ->add('role', ChoiceType::class, array(
                'choices' => array('Admin' => 'ROLE_ADMIN', 'User' => 'ROLE_USER')
            ));
    }

    public function getName() {
        return 'user';
    }
}
