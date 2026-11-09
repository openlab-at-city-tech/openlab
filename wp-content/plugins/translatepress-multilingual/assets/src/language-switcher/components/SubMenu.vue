<script setup>
    const items = [
        { path: '/floater', label: 'Floating' },
        { path: '/shortcode', label: 'Shortcode' },
        { path: '/menu-item', label: 'Menu Item' },
    ]
</script>

<template>
    <div class="trp-submenu__wrapper">
        <div class="trp-submenu">
            <RouterLink
                v-for="i in items"
                :key="i.path"
                :to="i.path"
                custom
                v-slot="{ href, navigate, isActive }"
            >
                <span
                    class="trp-submenu-item"
                    :class="{ 'trp-nav-active': isActive }"
                    role="link"
                    tabindex="0"
                    @click="navigate"
                    @keydown.enter.space.prevent="navigate"
                >
                  {{ i.label }}
                </span>
            </RouterLink>
        </div>
    </div>
</template>