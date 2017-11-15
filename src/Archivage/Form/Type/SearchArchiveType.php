<?php

namespace Archivage\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchArchiveType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('query');
    }

    public function getName() {
        return 'search_archive';
    }
}
