<template>
  <div class="main-content">
    <div class="header bg-gradient-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Sellfino App Store</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark m-0 p-0 text-sm font-weight-600 nobg">
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'apps'"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item active text-light">Pre-Order Creator</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading">Loading... <span class="badge ml-2"></span></div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" class="btn mb-1" v-for="tab in tabs" :class="{ 'btn-secondary' : tab.handle != tabActive, 'btn-info pointer-disabled' : tab.handle == tabActive }" @click="load(tab.handle)">{{ tab.name }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--5" v-if="tabActive">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col-6">
                  <h3 class="mb-0" v-if="tabActive == 'orders'">Orders with Pre-Order Products</h3>
                  <h3 class="mb-0" v-else>Manage Pre-Order Products <a href="#" class="btn btn-sm btn-primary ml-4 position-absolute" @click.prevent="modal = true" :class="{ disabled: loading }">Add Product</a></h3>
                </div>
                <div class="col-6">
                  <form class="float-right" @submit.prevent="searchRun" v-if="list.length">
                    <div class="row align-items-center m-0">
                      <div class="form-group m-0">
                        <input type="text" class="form-control text-xs h-auto py-1" :placeholder="searchPlaceholder" v-model="search">
                      </div>
                      <button class="btn btn-primary ml-2 btn-sm" type="submit">Find</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="table-responsive" v-if="tabActive == 'orders'">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-20">Order Name</th>
                    <th class="w-30">Order Date</th>
                    <th class="w-30">Customer</th>
                    <th class="w-10">Total</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <th scope="row">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a :href="admin_link(el.id, 'orders')" class="text-primary" target="_blank">{{ el.name }}</a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td>{{ date_format(el.created_at) }}</td>
                    <td>{{ el.customer }}</td>
                    <td>{{ Shopify.formatMoney(el.total, el.currency) }}</td>
                    <td class="text-right">
                      <a :href="admin_link(el.id, tabActive)" class="btn btn-icon-only text-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <table class="table align-items-center table-flush" v-else>
                <thead class="thead-light">
                  <tr>
                    <th>
                      <span v-if="loading">Loading... Please wait</span>
                      <span v-else>There are no elements here</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>

            <div class="table-responsive" v-if="tabActive == 'products'">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-30">Product Title</th>
                    <th class="w-20">ID</th>
                    <th class="w-10">Type</th>
                    <th class="w-20">Created At</th>
                    <th class="w-20"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <th scope="row">
                      <div class="media align-items-center">
                        <a class="avatar rounded-circle mr-3" v-if="el.image">
                          <img :src="img_url(el.image.src)">
                        </a>
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a href="" class="text-primary" @click.prevent="edit(el)">
                              {{ el.title }}
                            </a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td>{{ el.id }}</td>

                    <td v-if="el.type == 'preorder'">Pre-Order</td>
                    <td v-else>Coming Soon</td>

                    <td>{{ date_format(el.created_at) }}</td>
                    <td class="text-right">
                      <a href="" class="btn btn-icon-only" :class="{ 'text-muted' : !el.active, 'text-success' : el.active, disabled: loading }" @click.prevent="toggle(el)">
                        <i class="fas" :class="{ 'fa-toggle-off' : !el.active, 'fa-toggle-on' : el.active }"></i>
                      </a>
                      <a href="" class="btn btn-icon-only text-danger" @click.prevent="remove(el)" :class="{ disabled: loading }">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                      <a :href="admin_link(el.id, tabActive)" class="btn btn-icon-only text-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <table class="table align-items-center table-flush" v-else>
                <thead class="thead-light">
                  <tr>
                    <th>
                      <span v-if="loading">Loading... Please wait</span>
                      <span v-else>There are no elements here</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>

          </div>
          <div class="card-footer py-4 nobg">
            <nav>
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: page == 1 || loading }">
                  <a class="page-link" href="#" @click.prevent="prevPage">
                    <i class="fas fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: list.length < 50 || loading }">
                  <a class="page-link" href="#" @click.prevent="nextPage">
                    <i class="fas fa-angle-right"></i>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <component is="inc-PreOrderCreator-modal" :active="modal" />

  </div>
</template>

<script>
module.exports = {
  data: function() {
    return {
      loading: false,
      tabActive: 'orders',
      tabs: [
        { handle: 'orders', name: 'Orders' },
        { handle: 'products', name: 'Products' }
      ],
      list: [],
      page: 1,
      search: '',
      modal: false
   }
  },
  computed: {
    searchPlaceholder: function() {
      switch(this.tabActive) {
        case 'orders':
          return 'Enter order name'
          break;
        case 'products':
          return 'Enter product ID'
          break;
      }
    }
  },
  methods: {
    load: function(handle) {
      var self = this

      this.loading = true

      if (this.tabActive != handle) {
        this.page = 1
        this.search = ''
        this.tabActive = handle
      }

      url = '/app/PreOrderCreator/' + handle + '?page=' + this.page + '&search=' + this.search
      params = {
        method: 'GET',
        headers: this.$root.fetchHeaders
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.list = res
        self.loading = false
      })
      .catch((error) => {
        alert(error)
        self.loading = false
      })

    },
    edit: function(item) {
      this.$root.viewPrevData = {
        page: this.page,
        search: this.search
      }
      this.$root.viewData = item
      this.$root.view = 'PreOrderCreator-edit'
    },
    toggle: function(item) {
      item.active = !item.active

      url = '/app/PreOrderCreator/update'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify(item)
      }

      fetch(url, params)
      .then(errorCheck)
      .catch((error) => {
        alert(error)
      })
    },
    remove: function(item) {
      var self = this

      r = confirm('Are you sure?')
      if (r == true) {
        this.loading = true

        url = '/app/PreOrderCreator/delete'
        params = {
          method: 'POST',
          headers: this.$root.fetchHeaders,
          body: JSON.stringify({ id: item.id })
        }

        fetch(url, params)
        .then(errorCheck)
        .then(function(res) {
          self.loading = false
          if (self.list.length == 1 && self.page > 1) {
            self.page--
          }
          self.load('products')
        })
        .catch((error) => {
          alert(error)
          self.loading = false
        })
      }
    },
    searchRun: function() {
      this.page = 1
      this.loading = true
      this.load(this.tabActive)
    },
    nextPage: function() {
      this.page++
      this.load(this.tabActive)
    },
    prevPage: function() {
      this.page--
      this.load(this.tabActive)
    }
  },
  mounted: function() {
    if (this.$root.viewPrevData) {
      this.tabActive = 'products'
      this.page = this.$root.viewPrevData.page
      this.search = this.$root.viewPrevData.search
      this.$root.viewPrevData = null
    }

    this.load(this.tabActive)
  }
}
</script>