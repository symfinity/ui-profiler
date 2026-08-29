<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ParityTextType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [new NotBlank(message: 'parity.form.blank')],
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
