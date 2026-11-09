<template>
    <div>
        <p class="trp-no-available-suggestions" v-show="!available_suggestions">{{ editorStrings.translation_memory_no_suggestions }}</p>
        <transition name="fade">
            <details open v-show="available_suggestions">
              <summary>{{ editorStrings.translation_memory_suggestions }}</summary>
                <div class="trp-translation-memory-suggestions">
                    <span>
                        <div v-for="(suggestion, index) in suggestions" @click="copy(suggestion.translated)" :key="index" :title="editorStrings.translation_memory_click_to_copy">
                          <span class="percentage"><span>{{suggestion.similarity}}%</span></span>
                              <span class="translated">{{suggestion.translated}}</span>
                            <span class="original">
                                <template v-for="(part, partIndex) in suggestion.originalDiff" :key="partIndex">
                                    <template v-if="partIndex > 0">{{ ' ' }}</template>
                                    <ins v-if="part[0] === '+'">{{ part[1].join(' ') }}</ins>
                                    <del v-else-if="part[0] === '-'">{{ part[1].join(' ') }}</del>
                                    <template v-else>{{ part[1].join(' ') }}</template>
                                </template>
                            </span>
                        </div>
                    </span>
                </div>
            </details>
        </transition>
    </div>
</template>
<script>
    import axios      from 'axios'
    import autosize   from 'autosize'
    import simplediff from 'simplediff'
    import similarity from 'string-similarity'


    export default{
        props:[
            'value',
            'string',
            'editorStrings',
            'ajax_url',
            'nonces',
            'languageCode',
            'inputValue',
        ],
        data(){
            return{
                suggestions : [],
                available_suggestions : false,
                similarity : 0,
                currentstring : this.string
            }
        },
        mounted(){
            this.init()
        },
        methods:{
            init(){
                let data = new FormData()
                data.append( 'action', 'trp_get_similar_string_translation' )
                data.append( 'security', this.nonces[ 'getsimilarstring' ] )
                data.append( 'original_string', this.string.original )
                data.append( 'language', this.languageCode )
                data.append( 'type', this.string.type )
                data.append( 'number', 3 )

                let self = this
                axios.post(this.ajax_url, data)
                    .then(function (response) {
                        let suggestions = response.data
                        let i

                        for (i = suggestions.length - 1; i >= 0; --i) {
                            suggestions[i]['similarity'] = Math.round(similarity.compareTwoStrings(self.string.original,suggestions[i]['original'])*100)
                            // Render diff text through Vue interpolation; originals can contain untrusted HTML.
                            suggestions[i]['originalDiff'] = simplediff.stringDiff(self.string.original, suggestions[i]['original'])

                            if (suggestions[i]['similarity'] < 70 ) {
                                suggestions.splice(i, 1); // drop suggestions less then 70%
                            }
                        }

                        self.suggestions = suggestions
                        if (suggestions.length > 0){
                            self.available_suggestions = true
                        }

                    })
                    .catch(function (error) {
                        console.log(error)
                    });
            },
            copy(translated){
                this.currentstring.translationsArray[this.languageCode].editedTranslation = translated

                // why no store for you? Go with setTimeout.
                setTimeout(function(){
                    autosize.update(document.querySelectorAll('.trp-textarea'))
                }, 50);
            }
        }
    }
</script>
