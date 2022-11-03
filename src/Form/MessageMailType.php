<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\MessageMail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('message', TextareaType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('recipient', EntityType::class, [
                "attr" => ['class' => "form-control"],
                // looks for choices from this entity
                'class' => User::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'email',           
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageMail::class,
        ]);
    }
}
