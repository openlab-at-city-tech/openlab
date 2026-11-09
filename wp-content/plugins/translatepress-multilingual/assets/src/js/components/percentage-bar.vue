<template>
    <div class="trp-percentage-bar">
        <div class="trp-percentage-bar-inner" :style="{ width: getPercentage() }"></div>
        <div class="trp-filled-area" :style="{ width: getPercentage() }"></div>
    </div>
</template>

<script>
export default {
    props: ['defaultLanguage', 'currentLanguage', 'percentage'],
    methods: {
        getPercentage(){
            if ( this.percentage === 0 )
                return '0%';

            if ( this.defaultLanguage === this.currentLanguage )
                return this.percentage.defaultLanguage + '%';

            return this.percentage[this.currentLanguage] + '%';
        }
    }
}

</script>
