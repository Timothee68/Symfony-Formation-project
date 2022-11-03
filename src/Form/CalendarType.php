<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"]
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"]
            ])
            ->add('description', TextareaType::class ,[
                "attr" => [ 'class' => "form-control"]
                ])
            ->add('allDay')
            ->add('backgroundColor', ColorType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('borderColor', ColorType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('textColor', ColorType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
