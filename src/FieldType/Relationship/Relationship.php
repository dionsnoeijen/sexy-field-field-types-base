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

namespace Tardigrades\FieldType\Relationship;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadOptions;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;

class Relationship extends FieldType
{
    const MANY_TO_MANY = 'many-to-many';
    const ONE_TO_MANY = 'one-to-many';
    const MANY_TO_ONE = 'many-to-one';
    const ONE_TO_ONE = 'one-to-one';

    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection
    ): FormBuilderInterface {

        switch ($this->getConfig()->getRelationshipKind()) {
            case self::MANY_TO_MANY:
                return $this->addManyToManyToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity,
                    $section
                );
            case self::ONE_TO_MANY:
                return $this->addOneToManyToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity
                );
            case self::MANY_TO_ONE:
                return $this->addManyToOneToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity
                );
            case self::ONE_TO_ONE:
                return $this->addOneToOneToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity
                );
        }

        return $formBuilder;
    }

    private function addManyToManyToForm(
        FormBuilderInterface $formBuilder,
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity,
        SectionInterface $section
    ): FormBuilderInterface {

        $formOptions = $this->formOptions($sectionEntity);
        $fieldConfig = $this->getConfig()->toArray();
        $sectionHandle = $fieldConfig['field']['to'];

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($sectionHandle));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();


        $choices = $this->buildOptions($fullyQualifiedClassName, $fieldConfig, $readSection);

        $toHandle = $fieldConfig['field']['as'] ?? $sectionHandle;
        $toHandle = Inflector::pluralize($toHandle);

        $selectedEntities = null;
        $selectedEntitiesArray = null;
        if (!isset($formOptions['mapped']) || $formOptions['mapped']) {
            /** @var Collection $sectionEntities */
            $selectedEntities = $sectionEntity->{'get' . ucfirst($toHandle)}();
            $selectedEntitiesArray = $selectedEntities ? $selectedEntities->toArray() : null;
        }

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            array_merge([
                'choices' => $choices,
                'data' => $selectedEntitiesArray,
                'multiple' => true
            ], $this->formOptions($sectionEntity))
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($many) use ($sectionHandle, $readSection) {
                $entries = [];
                foreach ($many as $slug) {
                    try {
                        $entries[] = $readSection->read(
                            ReadOptions::fromArray([
                                ReadOptions::SECTION => $sectionHandle,
                                ReadOptions::SLUG => $slug
                            ])
                        )->current();
                    } catch (\Exception $exception) {}
                }
                return $entries;
            }
        ));

        return $formBuilder;
    }

    private function addOneToManyToForm(
        FormBuilderInterface $formBuilder,
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity
    ): FormBuilderInterface {

        $formOptions = $this->formOptions($sectionEntity);
        $fieldConfig = $this->getConfig()->toArray();
        $sectionHandle = $fieldConfig['field']['to'];

        $sectionTo = $sectionManager->readByHandle(Handle::fromString($sectionHandle));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['as'] ?? $sectionHandle;
        $toHandle = Inflector::pluralize($toHandle);

        $sectionEntities = null;
        $sectionEntitiesArray = null;
        if (!isset($formOptions['mapped']) || $formOptions['mapped']) {
            /** @var Collection $sectionEntities */
            $sectionEntities = $sectionEntity->{'get' . ucfirst($toHandle)}();
            $sectionEntitiesArray = $sectionEntities ? $sectionEntities->toArray() : null;
        }

        $choices = $this->buildOptions($fullyQualifiedClassName, $fieldConfig, $readSection);

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            array_merge([
                'choices' => $choices,
                'data' => $sectionEntitiesArray,
                'multiple' => true
            ], $this->formOptions($sectionEntity))
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($many) use ($sectionHandle, $readSection) {
                $entries = [];
                foreach ($many as $slug) {
                    try {
                        $entries[] = $readSection->read(
                            ReadOptions::fromArray([
                                ReadOptions::SECTION => $sectionHandle,
                                ReadOptions::SLUG => $slug
                            ])
                        )->current();
                    } catch (\Exception $exception) {}
                }
                return $entries;
            }
        ));

        return $formBuilder;
    }

    private function addManyToOneToForm(
        FormBuilderInterface $formBuilder,
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity
    ): FormBuilderInterface {

        $formOptions = $this->formOptions($sectionEntity);
        $fieldConfig = $this->getConfig()->toArray();
        $sectionHandle = $fieldConfig['field']['to'];
        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($sectionHandle));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['as'] ?? $sectionHandle;

        $selectedEntity = [];
        if (!isset($formOptions['mapped']) || $formOptions['mapped']) {
            $selectedEntity = $sectionEntity->{'get' . ucfirst($toHandle)}();
        }

        $choices = $this->buildOptions($fullyQualifiedClassName, $fieldConfig, $readSection);

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            array_merge([
                'choices' => $choices,
                'data' => $selectedEntity,
                'multiple' => false
            ], $formOptions)
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($one) use ($sectionHandle, $readSection) {
                $entry = null;
                if (!empty($one)) {
                    try {
                        $entry = $readSection->read(
                            ReadOptions::fromArray([
                                ReadOptions::SECTION => $sectionHandle,
                                ReadOptions::SLUG => $one
                            ])
                        )->current();
                    } catch (\Exception $exception) {}
                }
                return $entry;
            }
        ));

        return $formBuilder;
    }

    private function addOneToOneToForm(
        FormBuilderInterface $formBuilder,
        ReadSectionInterface $readSection,
        SectionManagerInterface $sectionManager,
        $sectionEntity
    ): FormBuilderInterface {

        $formOptions = $this->formOptions($sectionEntity);
        $fieldConfig = $this->getConfig()->toArray();
        $sectionHandle = $fieldConfig['field']['to'];
        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($sectionHandle));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['as'] ?? $sectionHandle;

        $selectedEntity = [];
        if (!isset($formOptions['mapped']) || $formOptions['mapped']) {
            $selectedEntity = $sectionEntity->{'get' . ucfirst($toHandle)}();
        }

        $choices = $this->buildOptions($fullyQualifiedClassName, $fieldConfig, $readSection);

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            array_merge([
                'choices' => $choices,
                'data' => $selectedEntity,
                'multiple' => false
            ], $this->formOptions($sectionEntity))
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($one) use ($sectionHandle, $readSection) {
                $entry = null;
                if (!empty($one)) {
                    try {
                        $entry = $readSection->read(
                            ReadOptions::fromArray([
                                ReadOptions::SECTION => $sectionHandle,
                                ReadOptions::SLUG => $one
                            ])
                        )->current();
                    } catch (\Exception $exception) {}
                }
                return $entry;
            }
        ));

        return $formBuilder;
    }

    private function buildOptions(
        FullyQualifiedClassName $fullyQualifiedClassName,
        array $fieldConfig,
        ReadSectionInterface $readSection
    ): array {

        $readOptions = [
            ReadOptions::SECTION => $fullyQualifiedClassName,
            ReadOptions::LIMIT => 100
        ];
        $nameExpression = [];

        try {
            $instructions = $fieldConfig['field']['form']['sexy-field-instructions']['relationship'];

            $readOptions[ReadOptions::LIMIT] = !empty($instructions['limit']) ?
                $instructions['limit'] :
                $readOptions[ReadOptions::LIMIT];

            if (!empty($instructions['field']) && !empty($instructions['value'])) {
                $readOptions[ReadOptions::FIELD] = [$instructions['field'] => $instructions['value']];
            }
            if (!empty($instructions['name-expression'])) {
                $nameExpression = explode('|', $instructions['name-expression']);
            }
        } catch (\Exception $exception) {
            // If no instructions it's fine, we have defaults
        }

        $entries = $readSection->read(ReadOptions::fromArray($readOptions));
        $choices = [ '...' => false ];
        foreach ($entries as $entry) {
            $name = $entry->getDefault();
            if ($nameExpression) {
                $find = $entry;
                foreach ($nameExpression as $method) {
                    if ($find) {
                        $find = $find->$method();
                    }
                }
                if ($find) {
                    $name = $find;
                }
            }

            // It's possible, in certain cases, that the
            // Name is double, prevent submission errors
            // by guaranteeing a unique name
            if (!empty($choices[$name])) {
                $name = $name . ' ' . rand(0, 9999999999);
            }

            $choices[$name] = (string) $entry->getSlug();
        }

        return $choices;
    }
}
