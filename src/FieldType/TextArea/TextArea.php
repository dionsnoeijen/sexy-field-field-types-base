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

namespace Tardigrades\FieldType\TextArea;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

class TextArea extends FieldType
{
    const TEXT_AREA_FIELD_TYPE = 'TextArea';

    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        Request $request
    ): FormBuilderInterface {

        $options = $this->formOptions($sectionEntity);

        $options['purify_html'] = empty($options['purify_html']) ? true : $options['purify_html'];

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            TextareaType::class,
            $options
        );

        return $formBuilder;
    }
}
