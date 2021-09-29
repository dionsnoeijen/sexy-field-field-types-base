<?php

declare(strict_types=1);

namespace Tardigrades\FieldType\Birthday;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

class Birthday extends FieldType
{
    const BIRTHDAY_FIELD_TYPE = 'Birthday';

    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        Request $request
    ): FormBuilderInterface {
        $options = $this->formOptions($sectionEntity);

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            BirthdayType::class,
            $options
        );

        return $formBuilder;
    }
}
