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
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

class DateTimeField extends FieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection
    ): FormBuilderInterface {

        if (!$this->hasEntityEvent('prePersist')) {
            $options = $this->formOptions($sectionEntity);

            // Default values
            if (!isset($options['format'])) {
                $options['format'] = DateTimeType::HTML5_FORMAT;
            }

            if (!isset($options['data'])) {
                $options['data'] = 'now';
            }

            // As a config value, pass the date time argument
            $options['data'] = new \DateTime($options['data']);

            $formBuilder->add(
                (string) $this->getConfig()->getHandle(),
                DateTimeType::class,
                $options
            );
        }

        return $formBuilder;
    }
}
