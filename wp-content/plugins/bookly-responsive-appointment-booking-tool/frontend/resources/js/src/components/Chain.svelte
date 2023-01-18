<script>
    import ChainItem from './ChainItem.svelte';

    export let items = [];
    export let data = {};
    export let multiple = false;

    let els = [];

    function onAddItem() {
        items.push({});
        items = items;
    }

    function onDropItem(event) {
        items.splice(event.detail, 1);
        items = items;
        els.splice(event.detail, 1);
    }

    export function validate() {
        return els.filter(el => !!el).map(el => el.validate());
    }

    export function getValues() {
        return els.filter(el => !!el).map(el => el.getValues());
    }
</script>

{#each items as item, index (item)}
    <ChainItem bind:this={els[index]} {...data} {item} {index} hasDropBtn="{multiple}" showDropBtn="{index > 0}" on:dropItem={onDropItem}/>
{/each}
{#if multiple}
    <div class="bookly-box">
        <button on:click="{onAddItem}" class="bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
            <span class="ladda-label">{data.l10n.add_service}</span>
        </button>
    </div>
{/if}