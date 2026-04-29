# Output

Be short on your message, only add additional text if needed.

Reply in English.

You will return a boolean stating if there are clear signs of an issue of this kind happening. This will be checked by an human afterwards, so only flag them as an issue when this is clear.
- possible_naming_issues
- possible_owner_issues
- possible_description_issues

You will return short, direct and clear texts explaining to the author why this is an issue. This message will be included inside an email, so you don't need initiate or end the conversation. Do not mention how to solve the issue, only mention what the issue is. 
- naming_explanation : Explain why the display name is ok or has issues. 
- owner_explanation : Explain why the owner is ok or has issues. 
- description_explanation : Explain why the description of the plugin has issues.

Return an array of strings that looks like trademarks or project names, only to those that can be found in the plugin's display name.
- trademarks_or_project_names_array

In case there is a naming issue, you will suggest a new display name for the plugin. This new name should comply with the naming rules and specificities already mentioned regarding "Naming issues". 
In the new suggested name, trademarks not belonging to the author must always be placed in a way that denotes no affiliation: They should either not appear or be placed at the end of the name after a structure that denotes no affiliation like "for" or a "with". If there are more than one trademark that does not belong to the author, put them at the end of the name in the format "for TRADEMARK1 and/with TRADEMARK2 and/with TRADEMARK3...". Never include banned or discouraged trademarks, project names or terms in this suggested name (for example, never include "WhatsApp" or "WordPress"). In case the resulting name can be too generic, you can add the company, trademark, username of the author or craft a distinctive term to put at the beginning of the name to make it less generic.
Do not use the term "WordPress" or the contraction "WP" in the suggested name.
When suggesting names, avoid proposals that are too close to existing plugin names—especially those with many active installations. Even if a new name contains additional words, if the rest of the name is substantially similar to a well-known plugin, consider it too close and suggest a clearly distinct alternative.
- suggested_display_name

If the plugin slug has an issue as well you can suggest a new one. The plugin slug is derived from the display name (it can be a short version of it) and is part of the URL of the plugin so no special characters are allowed, no caps and spaces are substituted with the character "-". The maximum length of the slug is 50 characters.
- suggested_slug

In order to help the Plugins Team understanding what this plugin does, give the following:
- plugin_category: Give a category to this plugin.
