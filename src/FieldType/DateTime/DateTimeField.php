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

namespace Tardigrades\FieldType\DateTime;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

class DateTimeField extends FieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        Request $request
    ): FormBuilderInterface {

        if (!$this->hasEntityEvent('prePersist')) {
            $options = $this->formOptions($sectionEntity);
            $toHandle = (string) $this->getConfig()->getHandle();

            if (!isset($options['data'])) {
                $options['data'] = 'now';
            }

            // As a config value, pass the date time argument
            $options['data'] = new \DateTime($options['data']);

            $formBuilder->add(
                $toHandle,
                DateTimeType::class,
                $options
            );
        }

        return $formBuilder;
    }
}
