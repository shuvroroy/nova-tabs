import DetailTabs from './components/DetailTabs.vue'
import FormTabs from './components/FormTabs.vue'

Nova.booting(app => {
  app.component('detail-tabs', DetailTabs)
  app.component('form-tabs', FormTabs)
})
