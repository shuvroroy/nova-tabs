<template>
  <div :class="darkModeClass">
    <div class="tab-group">
      <slot>
        <Heading :level="1" v-text="panel.name" v-if="panel.showTitle" />

        <p
          v-if="panel.helpText"
          :class="panel.helpText ? 'mt-2' : 'mt-3'"
          class="text-gray-500 text-sm font-semibold italic"
          v-html="panel.helpText"
        ></p>
      </slot>

      <div :class="[panel.showTitle && !panel.showToolbar ? 'mt-3' : '']">
        <div id="tabs">
          <div class="block">
            <nav
              aria-label="Tabs"
              class="isolate flex divide-x divide-gray-200 dark:divide-gray-700 rounded-lg shadow overflow-hidden overflow-scroll nova-tabs"
            >
              <a
                v-for="(tab, key) in getSortedTabs(tabs)"
                :key="key"
                :dusk="tab.slug + '-tab'"
                :class="
                  getIsTabCurrent(tab)
                    ? 'active text-' + getCurrentColor() + '-500 font-bold'
                    : 'font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                "
                class="block relative shrink-0 flex-grow overflow-hidden bg-white dark:bg-gray-800 py-4 px-4 text-center text-sm focus:z-10 cursor-pointer"
                @click.prevent="handleTabClick(tab)"
              >
                <span class="capitalize">{{ tab.properties.title }}</span>
                <span
                  v-if="getIsTabCurrent(tab)"
                  aria-hidden="true"
                  class="absolute inset-x-0 bottom-0 h-0.5"
                  :class="'bg-' + getCurrentColor() + '-500'"
                ></span>
              </a>
            </nav>
          </div>
        </div>

        <div
          v-for="(tab, index) in getSortedTabs(tabs)"
          :key="'related-tabs-fields' + index"
          :ref="getTabRefName(tab)"
          :class="['tab', tab.slug]"
          class="mt-3"
          :label="tab.name"
          v-show="getIsTabCurrent(tab)"
        >
          <div>
            <div
              v-if="getAllFieldsExceptRelatable(tab).length"
              class="bg-white dark:bg-gray-800 rounded-lg shadow py-2 px-6 divide-y divide-gray-100 dark:divide-gray-700"
            >
              <KeepAlive
                v-for="(field, index) in getAllFieldsExceptRelatable(tab)"
                :key="index"
              >
                <component
                  :is="getComponentName(field)"
                  class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0"
                  :class="{
                    'remove-bottom-border': index === tab.fields.length - 1,
                  }"
                  :field="field"
                  :index="index"
                  :resource="resource"
                  :resource-id="resourceId"
                  :resource-name="resourceName"
                  @actionExecuted="actionExecuted"
                />
              </KeepAlive>
            </div>
            <div v-if="getAllRelatableFields(tab).length">
              <KeepAlive
                v-for="(field, index) in getAllRelatableFields(tab)"
                :key="index"
              >
                <div class="mt-8">
                  <div
                    v-if="
                      getHeadingVisibilityForAttribute(tab, field.attribute)
                    "
                    class="border-b border-gray-400 dark:border-gray-700 pb-3 mb-5"
                  >
                    <h2
                      class="font-normal text-xl md:text-xl flex items-center"
                    >
                      <span>{{ field.name }}</span>
                    </h2>
                  </div>
                  <component
                    :is="getComponentName(field)"
                    :class="{
                      'remove-bottom-border': index === tab.fields.length - 1,
                    }"
                    :field="field"
                    :index="index"
                    :resource="resource"
                    :resource-id="resourceId"
                    :resource-name="resourceName"
                    @actionExecuted="actionExecuted"
                  />
                </div>
              </KeepAlive>
            </div>
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
    tabMode: 'detail',
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
