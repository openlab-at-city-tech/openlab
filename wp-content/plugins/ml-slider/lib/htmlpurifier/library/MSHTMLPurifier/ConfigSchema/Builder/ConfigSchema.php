<?php

/**
 * Converts MSHTMLPurifier_ConfigSchema_Interchange to our runtime
 * representation used to perform checks on user configuration.
 */
class MSHTMLPurifier_ConfigSchema_Builder_ConfigSchema
{

    /**
     * @param MSHTMLPurifier_ConfigSchema_Interchange $interchange
     * @return MSHTMLPurifier_ConfigSchema
     */
    public function build($interchange)
    {
        $schema = new MSHTMLPurifier_ConfigSchema();
        foreach ($interchange->directives as $d) {
            $schema->add(
                $d->id->key,
                $d->default,
                $d->type,
                $d->typeAllowsNull
            );
            if ($d->allowed !== null) {
                $schema->addAllowedValues(
                    $d->id->key,
                    $d->allowed
                );
            }
            foreach ($d->aliases as $alias) {
                $schema->addAlias(
                    $alias->key,
                    $d->id->key
                );
            }
            if ($d->valueAliases !== null) {
                $schema->addValueAliases(
                    $d->id->key,
                    $d->valueAliases
                );
            }
        }
        $schema->postProcess();
        return $schema;
    }
}

// vim: et sw=4 sts=4
