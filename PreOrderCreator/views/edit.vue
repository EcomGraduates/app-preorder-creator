<template>
  <div class="main-content">
    <div class="header bg-gradient-primary" :class="{ 'pb-6' : !loading }">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Sellfino App Store</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark m-0 p-0 text-sm font-weight-600 nobg">
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'apps'"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'PreOrderCreator-index'">Pre-Order Creator</a></li>
                  <li class="breadcrumb-item active text-light">Edit</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading">Loading... <span class="badge ml-2"></span></div>
              <a href="" class="btn btn-sm btn-neutral" v-if="!loading" :class="{ disabled: saving }" @click.prevent="save">Save</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--6" v-if="!loading">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">General <p class="m-0"><small>Set basic settings</small></p></h3>
            </div>
            <div class="card-body pt-0">
              <div class="row">
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="item-type" class="form-control-label">Type</label>
                    <select id="item-type" class="form-control" v-model="item.type">
                      <option value="preorder">Pre-Order Product</option>
                      <option value="comingsoon">Coming Soon Product</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="item-apply" class="form-control-label">Apply settings for</label>
                    <select id="item-apply" class="form-control" v-model="item.apply">
                      <option value="product">Product</option>
                      <option value="variants">Variants</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="item-button" class="form-control-label">Button Text</label>
                    <input type="text" id="item-button" class="form-control" v-model="item.button">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label for="item-note" class="form-control-label">Note - displayed in a line of the order item (cart, emails, etc)</label>
                    <textarea id="item-note" class="form-control" v-model="item.note"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt-5 mb-5" v-if="!loading">
      <div class="row" v-if="item.apply == 'variants' && item.variants.length > 1">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">Variants <p class="m-0"><small>Overwrite general settings for the specific variant</small></p></h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th class="w-20">Variant</th>
                    <th class="w-20">Type</th>
                    <th class="w-20">Button Text</th>
                    <th class="w-30">Note</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="variant in item.variants">
                    <td>{{ variant.title }}</td>
                    <td>
                      <select class="form-control" v-model="variant.type">
                        <option value="">Inherit</option>
                        <option value="preorder">Pre-Order Product</option>
                        <option value="comingsoon">Coming Soon Product</option>
                      </select>
                    </td>
                    <td><input type="text" class="form-control" v-model="variant.button"></td>
                    <td><input type="text" class="form-control" v-model="variant.note"></td>
                    <td class="text-right">
                      <a href="" class="btn btn-icon-only" :class="{ 'text-muted' : !variant.active, 'text-success' : variant.active, disabled: loading }" @click.prevent="variant.active = !variant.active">
                        <i class="fas" :class="{ 'fa-toggle-off' : !variant.active, 'fa-toggle-on' : variant.active }"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
module.exports = {
  data: function() {
    return {
      loading: true,
      saving: false,
      item: this.$root.viewData
    }
  },
  methods: {
    save: function() {
      var self = this, item = JSON.parse(JSON.stringify(this.item))

      this.saving = true

      if (item.apply == 'product') {
        item.variants = []
      }

      url = '/app/PreOrderCreator/update'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify(item)
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.saving = false
        self.$root.showToast('Product saved')
      })
      .catch((error) => {
        alert(error)
        self.saving = false
      })
    }
  },
  mounted: function() {
    var self = this

    if (!this.item.type) {
      this.item.type = 'preorder'
    }
    if (!this.item.apply) {
      Vue.set(this.item, 'apply', 'product')
    }
    if (!this.item.button) {
      this.item.button = 'Pre-order'
    }
    if (!this.item.note) {
      this.item.note = 'available on 01.01.1970'
    }
    if (!this.item.variants) {
      Vue.set(this.item, 'variants', []) 
    }

    url = '/api/shopify'
    params = {
      method: 'POST',
      headers: this.$root.fetchHeaders,
      body: JSON.stringify({
        endpoint: 'products/' + this.item.id,
        method: 'GET',
        params: { fields: 'id,title,variants' }
      })
    }

    fetch(url, params)
    .then(errorCheck)
    .then(function(res) {
      var variants = []
      
      if (self.item.variants.length) {
        res.product.variants.forEach(function(v) {
          found = false
          self.item.variants.forEach(function(variant, key) {
            if (v.id == variant.id) {
              self.item.variants[key].title = v.title
              variants.push(self.item.variants[key])
              found = true
            }
          })
          if (found == false) {
            variants.push({
              id: v.id,
              title: v.title,
              button: '',
              note: '',
              active: false,
              type: ''
            })
          }
        })

      } else {
        res.product.variants.forEach(function(variant) {
          variants.push({
            id: variant.id,
            title: variant.title,
            button: '',
            note: '',
            active: false,
            type: ''
          })
        })
      }

      self.item.variants = variants
      self.loading = false
    })
    .catch((error) => {
      alert(error)
    })
  }
}
</script>