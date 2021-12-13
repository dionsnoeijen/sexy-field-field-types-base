<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\FieldType\Choice;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

class Choice extends FieldType
{
    const CHOICE_FIELD_TYPE = 'Choice';

    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        Request $request
    ): FormBuilderInterface {
        $options = $this->formOptions($sectionEntity);

        if (!empty($options['choices'])) {
            foreach ($options['choices'] as &$choice) {
                if (is_null($choice)) {
                    $choice = false;
                }
            }
        }

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            ChoiceType::class,
            $options
        );

        return $formBuilder;
    }
}
