<script>
    import {createEventDispatcher, tick} from 'svelte';
    import jQuery from 'jquery';
    import Select from './Select.svelte';
    import { slide } from 'svelte/transition';

    export let item = {};
    export let index = 0;
    export let locations = [];
    export let categories = [];
    export let services = [];
    export let staff = [];
    export let defaults = {};
    export let required = {};
    export let servicesPerLocation = false;
    export let staffNameWithPrice = false;
    export let collaborativeHideStaff = false;
    export let showRatings = false;
    export let showCategoryInfo = false;
    export let showServiceInfo = false;
    export let showStaffInfo = false;
    export let maxQuantity = 1;
    export let hasLocationSelect = false;
    export let hasCategorySelect = true;
    export let hasServiceSelect = true;
    export let hasStaffSelect = true;
    export let hasDurationSelect = false;
    export let hasNopSelect = false;
    export let hasQuantitySelect = false;
    export let hasDropBtn = false;
    export let showDropBtn = false;
    export let l10n = {};
    export let date_from_element = null;

    const dispatch = createEventDispatcher();

    let locationId = 0;
    let categoryId = 0;
    let serviceId = 0;
    let staffId = 0;
    let duration = 1;
    let nop = 1;
    let quantity = 1;

    let categoryItems;
    let serviceItems;
    let staffItems;
    let durationItems;
    let nopItems;
    let quantityItems;

    let locationPlaceholder;
    let categoryPlaceholder;
    let servicePlaceholder;
    let staffPlaceholder;

    let locationError, locationEl;
    let serviceError, serviceEl;
    let staffError, staffEl;

    let lookupLocationId;
    let categorySelected;
    let maxCapacity;
    let minCapacity;
    let srvMaxCapacity;
    let srvMinCapacity;

    $: {
        lookupLocationId = (servicesPerLocation && locationId) ? locationId : 0;
        categoryItems = {};
        serviceItems = {};
        staffItems = {};
        nopItems = {};

        // Staff
        jQuery.each(staff, (id, staffMember) => {
            if (!locationId || id in locations[locationId].staff) {
                if (!serviceId) {
                    if (!categoryId) {
                        staffItems[id] = jQuery.extend({}, staffMember);
                    } else {
                        jQuery.each(staffMember.services, srvId => {
                            if (services[srvId].category_id === categoryId) {
                                staffItems[id] = jQuery.extend({}, staffMember);
                                return false;
                            }
                        });
                    }
                } else if (serviceId in staffMember.services) {
                    jQuery.each(staffMember.services[serviceId].locations, (locId, locSrv) => {
                        if (lookupLocationId && lookupLocationId !== parseInt(locId)) {
                            return true;
                        }
                        srvMinCapacity = srvMinCapacity ? Math.min(srvMinCapacity, locSrv.min_capacity) : locSrv.min_capacity;
                        srvMaxCapacity = srvMaxCapacity ? Math.max(srvMaxCapacity, locSrv.max_capacity) : locSrv.max_capacity;
                        staffItems[id] = jQuery.extend({}, staffMember, {
                            name: staffMember.name + (
                                staffNameWithPrice && locSrv.price !== null && (lookupLocationId || !servicesPerLocation)
                                    ? ' (' + locSrv.price + ')'
                                    : ''
                            ),
                            hidden: collaborativeHideStaff && services[serviceId].type === 'collaborative',
                        });
                        if (collaborativeHideStaff && services[serviceId].type === 'collaborative') {
                            staffId = 0;
                        }
                    });
                }
            }
        });
        // Add ratings to staff names
        if (showRatings) {
            jQuery.each(staff, (id, staffMember) => {
                if (staffMember.id in staffItems) {
                    if (serviceId) {
                        if (serviceId in staffMember.services && staffMember.services[serviceId].rating) {
                            staffItems[staffMember.id].name = '★' + staffMember.services[serviceId].rating + ' ' + staffItems[staffMember.id].name;
                        }
                    } else if (staffMember.rating) {
                        staffItems[staffMember.id].name = '★' + staffMember.rating + ' ' + staffItems[staffMember.id].name;
                    }
                }
            });
        }

        // Category & service
        if (!locationId) {
            categoryItems = categories;
            jQuery.each(services, (id, service) => {
                if (!categoryId || !categorySelected || service.category_id === categoryId) {
                    if (!staffId || id in staff[staffId].services) {
                        serviceItems[id] = service;
                    }
                }
            });
        } else {
            let categoryIds = [],
                serviceIds  = [];
            if (servicesPerLocation) {
                jQuery.each(staff, stId => {
                    jQuery.each(staff[stId].services, srvId => {
                        if (lookupLocationId in staff[stId].services[srvId].locations) {
                            categoryIds.push(services[srvId].category_id);
                            serviceIds.push(srvId);
                        }
                    });
                });
            } else {
                jQuery.each(locations[locationId].staff, stId => {
                    jQuery.each(staff[stId].services, srvId => {
                        categoryIds.push(services[srvId].category_id);
                        serviceIds.push(srvId);
                    });
                });
            }
            jQuery.each(categories, (id, category) => {
                if (jQuery.inArray(parseInt(id), categoryIds) > -1) {
                    categoryItems[id] = category;
                }
            });
            if (categoryId && jQuery.inArray(categoryId, categoryIds) === -1) {
                categoryId = 0;
                categorySelected = false;
            }
            jQuery.each(services, (id, service) => {
                if (jQuery.inArray(id, serviceIds) > -1) {
                    if (!categoryId || !categorySelected || service.category_id === categoryId) {
                        if (!staffId || id in staff[staffId].services) {
                            serviceItems[id] = service;
                        }
                    }
                }
            });
        }

        // Number of persons
        maxCapacity = serviceId
            ? (staffId
                ? (lookupLocationId in staff[staffId].services[serviceId].locations
                    ? staff[staffId].services[serviceId].locations[lookupLocationId].max_capacity :
                    1)
                : srvMaxCapacity ? srvMaxCapacity : 1)
            : 1;
        minCapacity = serviceId
            ? (staffId
                ? (lookupLocationId in staff[staffId].services[serviceId].locations
                    ? staff[staffId].services[serviceId].locations[lookupLocationId].min_capacity :
                    1)
                : srvMinCapacity ? srvMinCapacity : 1)
            : 1;

        for (let i = minCapacity; i <= maxCapacity; ++i) {
            nopItems[i] = {id: i, name: i};
        }
        if (nop > maxCapacity) {
            nop = maxCapacity;
        }
        if (nop < minCapacity || !hasNopSelect) {
            nop = minCapacity;
        }

        // Duration
        durationItems = {1: {id: 1, name: '-'}};
        if (serviceId) {
            if (!staffId || servicesPerLocation && !locationId) {
                if ('units' in services[serviceId]) {
                    durationItems = services[serviceId].units;
                }
            } else {
                let locId = locationId || 0;
                let staffLocations = staff[staffId].services[serviceId].locations;
                if (staffLocations) {
                    let staffLocation = locId in staffLocations ? staffLocations[locId] : staffLocations[0];
                    if ('units' in staffLocation) {
                        durationItems = staffLocation.units;
                    }
                }
            }
        }
        if (!(duration in durationItems)) {
            if (Object.keys(durationItems).length > 0) {
                duration = Object.values(durationItems)[0].id;
            } else {
                duration = 1;
            }
        }

        // Quantity
        quantityItems = {};
        for (let q = 1; q <= maxQuantity; ++q) {
            quantityItems[q] = {id: q, name: q};
        }

        // Placeholders
        locationPlaceholder = {id: 0, name: l10n.location_option};
        categoryPlaceholder = {id: 0, name: l10n.category_option};
        servicePlaceholder = {id: 0, name: l10n.service_option};
        staffPlaceholder = {id: 0, name: l10n.staff_option};
    }

    // Preselect values
    tick().then(() => {
        // Location
        let selected = item.location_id || defaults.location_id;
        if (selected) {
            onLocationChange({detail: selected});
        }
    }).then(() => {
        // Category
        if (defaults.category_id) {
            onCategoryChange({detail: defaults.category_id});
        }
    }).then(() => {
        // Service
        let selected = item.service_id || defaults.service_id;
        if (selected) {
            onServiceChange({detail: selected});
        }
    }).then(() => {
        // Staff
        let selected;
        if (hasStaffSelect && item.staff_ids && item.staff_ids.length) {
            selected = item.staff_ids.length > 1 ? 0 : item.staff_ids[0];
        } else {
            selected = defaults.staff_id;
        }
        if (selected) {
            onStaffChange({detail: selected});
        }
    }).then(() => {
        // Duration
        if (item.units > 1) {
            onDurationChange({detail: item.units});
        }
    }).then(() => {
        // Nop
        if (item.number_of_persons > 1) {
            onNopChange({detail: item.number_of_persons});
        }
    }).then(() => {
        // Quantity
        if (item.quantity > 1) {
            onQuantityChange({detail: item.quantity});
        }
    });

    function onLocationChange(event) {
        locationId = event.detail;
        // Validate value
        if (!(locationId in locations)) {
            locationId = 0;
        }
        // Update related values
        if (locationId) {
            let lookupLocationId = servicesPerLocation ? locationId : 0;
            if (staffId) {
                if (!(staffId in locations[locationId].staff)) {
                    staffId = 0;
                } else if (serviceId && !(lookupLocationId in staff[staffId].services[serviceId].locations)) {
                    staffId = 0;
                }
            }
            if (serviceId) {
                let valid = false;
                jQuery.each(locations[locationId].staff, id => {
                    if (serviceId in staff[id].services && lookupLocationId in staff[id].services[serviceId].locations) {
                        valid = true;
                        return false;
                    }
                });
                if (!valid) {
                    serviceId = 0;
                }
            }
            if (categoryId) {
                let valid = false;
                jQuery.each(locations[locationId].staff, id => {
                    jQuery.each(staff[id].services, srvId => {
                        if (services[srvId].category_id === categoryId) {
                            valid = true;
                            return false;
                        }
                    });
                    if (valid) {
                        return false;
                    }
                });
                if (!valid) {
                    categoryId = 0;
                }
            }
        }
    }

    function onCategoryChange(event) {
        categoryId = event.detail;
        // Validate value
        if (!(categoryId in categoryItems)) {
            categoryId = 0;
        }
        // Update related values
        if (categoryId) {
            categorySelected = true;
            if (serviceId) {
                if (services[serviceId].category_id !== categoryId) {
                    serviceId = 0;
                }
            }
            if (staffId) {
                let valid = false;
                jQuery.each(staff[staffId].services, id => {
                    if (services[id].category_id === categoryId) {
                        valid = true;
                        return false;
                    }
                });
                if (!valid) {
                    staffId = 0;
                }
            }
        } else {
            categorySelected = false;
        }
    }

    function onServiceChange(event) {
        let dateMin = false;
        srvMinCapacity = false;
        srvMaxCapacity = false;
        serviceId = event.detail;
        // Validate value
        if (!(serviceId in serviceItems)) {
            serviceId = 0;
        }
        // Update related values
        if (serviceId) {
            categoryId = services[serviceId].category_id;
            if (staffId && !(serviceId in staff[staffId].services)) {
                staffId = 0;
            }
            if (date_from_element[0]) {
                dateMin = services[serviceId].hasOwnProperty('min_time_prior_booking') ? services[serviceId].min_time_prior_booking : date_from_element.data('date_min');
            }
        } else if (!categorySelected) {
            categoryId = 0;
            if (date_from_element[0]) {
                dateMin = date_from_element.data('date_min');
            }
        }
        if (date_from_element[0]) {
            date_from_element.pickadate('picker').set('min', dateMin);
            if (date_from_element.data('updated')) {
                date_from_element.pickadate('picker').set('select', date_from_element.pickadate('picker').get('select'));
            } else {
                date_from_element.pickadate('picker').set('select', dateMin);
            }
        }
    }

    function onStaffChange(event) {
        staffId = event.detail;
        // Validate value
        if (!(staffId in staffItems)) {
            staffId = 0;
        }
    }

    function onDurationChange(event) {
        duration = event.detail;
        // Validate value
        if (!(duration in durationItems)) {
            duration = 1;
        }
    }

    function onNopChange(event) {
        nop = event.detail;
        // Validate value
        if (!(nop in nopItems)) {
            nop = 1;
        }
    }

    function onQuantityChange(event) {
        quantity = event.detail;
        // Validate value
        if (!(quantity in quantityItems)) {
            quantity = 1;
        }
    }

    function onDropBtnClick() {
        dispatch('dropItem', index);
    }

    export function validate() {
        let valid = true;
        let el = null;

        staffError = serviceError = locationError = null;

        if (required.staff && !staffId && (!collaborativeHideStaff || !serviceId || services[serviceId].type !== 'collaborative')) {
            valid = false;
            staffError = l10n.staff_error;
            el = staffEl;
        }
        if (!serviceId) {
            valid = false;
            serviceError = l10n.service_error;
            el = serviceEl;
        }
        if (required.location && !locationId) {
            valid = false;
            locationError = l10n.location_error;
            el = locationEl;
        }

        return {valid, el};
    }

    export function getValues() {
        return {
            locationId: locationId,
            categoryId: categoryId,
            serviceId: serviceId,
            staffIds: staffId ? [staffId] : jQuery.map(staffItems, item => item.id),
            duration: duration,
            nop: nop,
            quantity: quantity,
        };
    }
</script>

<div class="bookly-table bookly-box">
    {#if hasLocationSelect}
        <div class="bookly-form-group" data-type="location">
            <Select
                    bind:el={locationEl}
                    label="{l10n.location_label}"
                    placeholder="{locationPlaceholder}"
                    items="{Object.values(locations)}"
                    selected="{locationId}"
                    error="{locationError}"
                    on:change="{onLocationChange}"
            />
        </div>
    {/if}
    {#if hasCategorySelect}
        <div class="bookly-form-group" data-type="category">
            <Select
                    label="{l10n.category_label}"
                    placeholder="{categoryPlaceholder}"
                    items="{Object.values(categoryItems)}"
                    selected="{categoryId}"
                    on:change="{onCategoryChange}"
            />
        </div>
        {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}
            <div class="bookly-box bookly-visible-sm bookly-category-info" transition:slide>
                {@html categories[categoryId].info}
            </div>
        {/if}
    {/if}
    {#if hasServiceSelect}
        <div class="bookly-form-group" data-type="service">
            <Select
                    bind:el={serviceEl}
                    label="{l10n.service_label}"
                    placeholder="{servicePlaceholder}"
                    items="{Object.values(serviceItems)}"
                    selected="{serviceId}"
                    error="{serviceError}"
                    on:change="{onServiceChange}"
            />
        </div>
        {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}
            <div class="bookly-box bookly-visible-sm bookly-service-info" transition:slide>
                {@html services[serviceId].info}
            </div>
        {/if}
    {/if}
    {#if hasStaffSelect}
        <div class="bookly-form-group" data-type="staff">
            <Select
                    bind:el={staffEl}
                    label="{l10n.staff_label}"
                    placeholder="{staffPlaceholder}"
                    items="{Object.values(staffItems)}"
                    selected="{staffId}"
                    error="{staffError}"
                    on:change="{onStaffChange}"
            />
        </div>
        {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}
            <div class="bookly-box bookly-visible-sm bookly-staff-info" transition:slide>
                {@html staff[staffId].info}
            </div>
        {/if}
    {/if}
    {#if hasDurationSelect}
        <div class="bookly-form-group" data-type="duration">
            <Select
                    label="{l10n.duration_label}"
                    items="{Object.values(durationItems)}"
                    selected="{duration}"
                    on:change="{onDurationChange}"
            />
        </div>
    {/if}
    {#if hasNopSelect}
        <div class="bookly-form-group" data-type="nop">
            <Select
                    label="{l10n.nop_label}"
                    items="{Object.values(nopItems)}"
                    selected="{nop}"
                    on:change="{onNopChange}"
            />
        </div>
    {/if}
    {#if hasQuantitySelect}
        <div class="bookly-form-group" data-type="quantity">
            <Select
                    label="{l10n.quantity_label}"
                    items="{Object.values(quantityItems)}"
                    selected="{quantity}"
                    on:change="{onQuantityChange}"
            />
        </div>
    {/if}
    {#if hasDropBtn}
        <div class="bookly-form-group bookly-chain-actions">
            <label></label>
            <div>
                {#if showDropBtn}
                    <button class="bookly-round" on:click={onDropBtnClick}><i class="bookly-icon-sm bookly-icon-drop"></i></button>
                {/if}
            </div>
        </div>
    {/if}
</div>
{#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}
    <div class="bookly-box bookly-visible-md bookly-category-info" transition:slide>
        {@html categories[categoryId].info}
    </div>
{/if}
{#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}
    <div class="bookly-box bookly-visible-md bookly-service-info" transition:slide>
        {@html services[serviceId].info}
    </div>
{/if}
{#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}
    <div class="bookly-box bookly-visible-md bookly-staff-info" transition:slide>
        {@html staff[staffId].info}
    </div>
{/if}
