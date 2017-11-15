<?php

namespace Archivage\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SearchByPeriodType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date_min', DateType::class)
            ->add('date_max', DateType::class);
    }

    public function getName() {
        return 'search_by_period';
    }
}
