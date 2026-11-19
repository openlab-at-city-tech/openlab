<?php

/**
 * Validate all attributes in the tokens.
 */

class MSHTMLPurifier_Strategy_ValidateAttributes extends MSHTMLPurifier_Strategy
{

    /**
     * @param MSHTMLPurifier_Token[] $tokens
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return MSHTMLPurifier_Token[]
     */
    public function execute($tokens, $config, $context)
    {
        // setup validator
        $validator = new MSHTMLPurifier_AttrValidator();

        $token = false;
        $context->register('CurrentToken', $token);

        foreach ($tokens as $key => $token) {

            // only process tokens that have attributes,
            //   namely start and empty tags
            if (!$token instanceof MSHTMLPurifier_Token_Start && !$token instanceof MSHTMLPurifier_Token_Empty) {
                continue;
            }

            // skip tokens that are armored
            if (!empty($token->armor['ValidateAttributes'])) {
                continue;
            }

            // note that we have no facilities here for removing tokens
            $validator->validateToken($token, $config, $context);
        }
        $context->destroy('CurrentToken');
        return $tokens;
    }
}

// vim: et sw=4 sts=4
