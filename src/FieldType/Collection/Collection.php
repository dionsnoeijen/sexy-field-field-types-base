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

namespace Tardigrades\FieldType\Collection;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

class Collection extends FieldType
{
    const COLLECTION_FIELD_TYPE = 'Collection';

    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        Request $request
    ): FormBuilderInterface {

        $options = $this->formOptions($sectionEntity);

        if (empty($options['entry_type'])) {
            $options['entry_type'] = TextType::class;
        }

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            CollectionType::class,
            $options
        );

        return $formBuilder;
    }
}
