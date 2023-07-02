<template>
  <div :class="darkModeClass">
    <div class="tab-group">
      <slot>
        <Heading v-if="panel.showTitle" :level="1" v-text="panel.name" />

        <p
          v-if="panel.helpText"
          :class="panel.helpText ? 'tabs-mt-2' : 'tabs-mt-3'"
          class="tabs-text-gray-500 tabs-text-sm tabs-font-semibold tabs-italic"
          v-html="panel.helpText"
        ></p>
      </slot>

      <div
        class="shadow bg-white dark:bg-gray-800 rounded-b-lg rounded-t-lg"
        :class="[panel.showTitle && !panel.showToolbar ? 'tabs-mt-3' : '']"
      >
        <div id="tabs">
          <div class="block">
            <nav
              aria-label="Tabs"
              class="isolate flex divide-x divide-gray-200 dark:divide-gray-700 rounded-t-lg shadow overflow-hidden overflow-scroll nova-tabs"
            >
              <a
                v-for="(tab, key) in getSortedTabs(tabs)"
                :key="key"
                :class="
                  getIsTabCurrent(tab)
                    ? 'active text-' + getCurrentColor() + '-500 font-bold'
                    : 'font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                "
                :dusk="tab.slug + '-tab'"
                :ref="tab.slug + '-tab'"
                class="block relative shrink-0 flex-grow overflow-hidden bg-white dark:bg-gray-800 py-4 px-4 text-center text-sm focus:z-10 cursor-pointer"
                @click.prevent="handleTabClick(tab)"
              >
                <span class="capitalize">{{ tab.properties.title }}</span>
                <span
                  aria-hidden="true"
                  class="absolute inset-x-0 bottom-0 h-0.5"
                  :class="[
                    getIsTabCurrent(tab)
                      ? 'bg-' + getCurrentColor() + '-500'
                      : 'bg-gray-200 dark:bg-gray-700',
                  ]"
                ></span>
              </a>
            </nav>
          </div>
        </div>

        <div
          v-for="(tab, index) in getSortedTabs(tabs)"
          v-show="getIsTabCurrent(tab)"
          :key="'related-tabs-fields' + index"
          :ref="getTabRefName(tab)"
          :class="['tab', getIsTabCurrent(tab) ? 'block' : 'hidden', tab.slug]"
          :label="tab.name"
        >
          <div
            class="bg-white dark:bg-gray-800 rounded-b-lg shadow py-2 divide-y divide-gray-100 dark:divide-gray-700"
          >
            <KeepAlive>
              <template
                v-for="(field, index) in tab.fields"
                :key="'tab-' + index"
              >
                <component
                  v-if="!field.from"
                  :is="getComponentName(field)"
                  ref="fields"
                  :class="{
                    'remove-bottom-border': index === tab.fields.length - 1,
                  }"
                  :errors="validationErrors"
                  :field="field"
                  :form-unique-id="formUniqueId"
                  :related-resource-id="relatedResourceId"
                  :related-resource-name="relatedResourceName"
                  :resource-id="resourceId"
                  :resource-name="resourceName"
                  :show-help-text="field.helpText != null"
                  :shown-via-new-relation-modal="shownViaNewRelationModal"
                  :via-relationship="viaRelationship"
                  :via-resource="viaResource"
                  :via-resource-id="viaResourceId"
                  @field-changed="$emit('field-changed')"
                  @file-deleted="$emit('update-last-retrieved-at-timestamp')"
                  @file-upload-started="$emit('file-upload-started')"
                  @file-upload-finished="$emit('file-upload-finished')"
                />

                <component
                  v-if="field.from"
                  :is="getComponentName(field)"
                  :errors="validationErrors"
                  :resource-id="getResourceId(field)"
                  :resource-name="field.resourceName || resourceName"
                  :field="field"
                  :via-resource="field.from.viaResource"
                  :via-resource-id="field.from.viaResourceId"
                  :via-relationship="field.from.viaRelationship"
                  :form-unique-id="relationFormUniqueId"
                  @field-changed="$emit('field-changed')"
                  @file-deleted="$emit('update-last-retrieved-at-timestamp')"
                  @file-upload-started="$emit('file-upload-started')"
                  @file-upload-finished="$emit('file-upload-finished')"
                  :show-help-text="field.helpText != null"
                />
              </template>
            </KeepAlive>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BehavesAsPanel from '../mixins/BehavesAsPanel'
import HasTabs from '../mixins/HasTabs'

export default {
  mixins: [BehavesAsPanel, HasTabs],
  data: () => ({
    tabMode: 'form',
  }),
  methods: {
    getAllFieldsExceptRelatable(tab) {
      return tab.fields.filter(field => !field.relatable)
    },
    getAllRelatableFields(tab) {
      return tab.fields.filter(field => field.relatable)
    },
  },
}
</script>
