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

use Doctrine\Common\Util\Inflector;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Service\ReadOptions;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
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
        $sectionEntity,
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
                break;
            case self::ONE_TO_MANY:
                $this->addOneToManyToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity
                );
                break;
            case self::MANY_TO_ONE:
                $this->addManyToOneToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity
                );
                break;
            case self::ONE_TO_ONE:
                $this->addOneToOneToForm(
                    $formBuilder,
                    $readSection,
                    $sectionManager,
                    $sectionEntity
                );
                break;
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

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($fieldConfig['field']['to']));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        try {
            $entries = $readSection->read(ReadOptions::fromArray([
                'section' => $fullyQualifiedClassName
            ]));
        } catch (\Exception $exception) {
            $entries = [];
        }

        $choices = [];
        foreach ($entries as $entry) {
            $choices[$entry->getDefault()] = (string) $entry->getSlug();
        }

        $sectionHandle = $fieldConfig['field']['to'];
        $toHandle = Inflector::pluralize($sectionHandle);
        $selectedEntities = $sectionEntity->{'get' . ucfirst($toHandle)}();

        $selectedEntitiesArray = $selectedEntities ? $selectedEntities->toArray() : null;

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            [
                'choices' => $choices,
                'data' => $selectedEntitiesArray,
                'multiple' => true
            ]
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($many) use ($sectionHandle, $readSection) {
                $entries = [];
                foreach ($many as $slug) {
                    $entries[] = $readSection->read(
                        ReadOptions::fromArray([
                            ReadOptions::SECTION => $sectionHandle,
                            ReadOptions::SLUG => $slug
                        ])
                    )->current();
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

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($fieldConfig['field']['to']));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['as'] ?? $fieldConfig['field']['to'];
        $toHandle = Inflector::pluralize($toHandle);

        $sectionHandle = $fieldConfig['field']['to'];
        $sectionEntities = $sectionEntity->{'get' . ucfirst($toHandle)}();
        $sectionEntitiesArray = $sectionEntities ? $sectionEntities->toArray() : null;

        try {
            $entries = $readSection->read(ReadOptions::fromArray([
                'section' => $fullyQualifiedClassName
            ]));
        } catch (\Exception $exception) {
            $entries = [];
        }

        $choices = [];
        foreach ($entries as $entry) {
            $choices[$entry->getDefault()] = (string) $entry->getSlug();
        }

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            [
                'choices' => $choices,
                'data' => $sectionEntitiesArray,
                'multiple' => true
            ]
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($many) use ($sectionHandle, $readSection) {
                $entries = [];
                foreach ($many as $slug) {
                    $entries[] = $readSection->read(
                        ReadOptions::fromArray([
                            ReadOptions::SECTION => $sectionHandle,
                            ReadOptions::SLUG => $slug
                        ])
                    )->current();
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

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($fieldConfig['field']['to']));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['as'] ?? $fieldConfig['field']['to'];
        $selectedEntity = $sectionEntity->{'get' . ucfirst($toHandle)}();

        try {
            $entries = $readSection->read(ReadOptions::fromArray([
                'section' => $fullyQualifiedClassName
            ]));
        } catch (\Exception $exception) {
            $entries = [];
        }

        $choices = [ '...' => false ];
        foreach ($entries as $entry) {
            $choices[$entry->getDefault()] = (string) $entry->getSlug();
        }

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            [
                'choices' => $choices,
                'data' => $selectedEntity,
                'multiple' => false
            ]
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($slug) use ($toHandle, $readSection) {
                return $readSection->read(
                    ReadOptions::fromArray([
                        ReadOptions::SECTION => $toHandle,
                        ReadOptions::SLUG => $slug
                    ])
                )->current();
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

        $fieldConfig = $this->getConfig()->toArray();

        $sectionTo = $sectionManager
            ->readByHandle(Handle::fromString($fieldConfig['field']['to']));

        $fullyQualifiedClassName = $sectionTo
            ->getConfig()
            ->getFullyQualifiedClassName();

        $toHandle = $fieldConfig['field']['as'] ?? $fieldConfig['field']['to'];
        $selectedEntity = $sectionEntity->{'get' . ucfirst($toHandle)}();

        try {
            $entries = $readSection->read(ReadOptions::fromArray([
                'section' => $fullyQualifiedClassName
            ]));
        } catch (\Exception $exception) {
            $entries = [];
        }

        $choices = ['...' => false];
        foreach ($entries as $entry) {
            $choices[(string) $entry->getDefault()] = (string) $entry->getSlug();
        }

        $formBuilder->add(
            $toHandle,
            ChoiceType::class,
            [
                'choices' => $choices,
                'data' => $selectedEntity,
                'multiple' => false
            ]
        );

        $formBuilder->get($toHandle)->addModelTransformer(new CallbackTransformer(
            function () { return; },
            function ($slug) use ($toHandle, $readSection) {
                return $readSection->read(
                    ReadOptions::fromArray([
                        ReadOptions::SECTION => $toHandle,
                        ReadOptions::SLUG => $slug
                    ])
                )->current();
            }
        ));

        return $formBuilder;
    }
}
