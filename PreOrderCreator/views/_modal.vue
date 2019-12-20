<template>
  <div>
    <div class="modal fade" style="display:block" :class="{ show: active }">
      <div class="modal-dialog modal- modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card bg-secondary border-0 mb-0">
              <button type="button" class="close" @click="$parent.modal = false">
                <span>×</span>
              </button>
              <div class="card-header bg-transparent pb-1">
                <div class="mt-0 mb-0 h3">Products</div>
                <div class="mb-3 h5">Choose which product should be used for Pre-Order</div>
                <form @submit.prevent="searchRun">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control text-xs h-auto" placeholder="Enter product id..." v-model="search">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-primary btn-md lh-120" type="submit" :class="{ disabled: loading || searching }">
                        <span v-if="searching">Searching ...</span>
                        <span v-else>Search</span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="card-body p-0">
                <div class="loading-skeleton" v-if="loading"><div></div><div></div><div></div></div>
                <div v-else>

                  <div class="table-responsive" v-if="list.length">
                    <table class="table text-wrap align-items-center table-flush">
                      <thead class="thead-light">
                        <tr>
                          <th class="w-60 border-top-0">Name</th>
                          <th class="w-30 border-top-0">ID</th>
                          <th class="w-10 border-top-0"></th>
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
                                  <a href="" class="text-primary" @click.prevent="select(el)">{{ el.title }}</a>
                                </span>
                              </div>
                            </div>
                          </th>
                          <td>
                            {{ el.id }}
                          </td>
                          <td class="text-right">
                            <a :href="admin_link(el.id, 'products')" class="btn btn-icon-only text-primary" target="_blank">
                              <i class="fas fa-external-link-alt"></i>
                            </a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="row py-lg-2" v-else>
                    <div class="col-12">
                      <div class="alert alert-primary bg-gradient-primary" role="alert">
                        <span class="alert-inner--icon mr-2"><i class="fa fa-info-circle"></i></span>
                        <span class="alert-inner--text font-italic">No products found</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <nav>
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: page.prev == null || loading }">
                  <a class="page-link" href="#" @click.prevent="prevPage">
                    <i class="fas fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: page.next == null || loading }">
                  <a class="page-link" href="#" @click.prevent="nextPage">
                    <i class="fas fa-angle-right"></i>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
            <div>
              <button type="button" class="btn btn-secondary" @click="$parent.modal = false">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop fade" :class="{ show: active }"></div>

  </div>
</template>

<script>
module.exports = {
  props: ['active'],
  data: function() {
    return {
      loading: true,
      searching: true,
      list: [],
      page: {
        prev: null,
        next: 1
      },
      search: ''
    }
  },
  watch: {
    active: function(val) {
      if (val) {
        this.loading = true
        this.searching = true
        this.page = {
          prev: null,
          next: 1
        },
        this.search = ''
        this.getProducts()
      }
    }
  },
  methods: {
    getProducts: function() {
      var self = this

      url = '/api/shopify'
      data = {
        page_info: this.page.next === 1 ? null : this.page.next,
        fields: 'id,title,image'
      }

      if (this.search != '') {
        data.ids = this.search
      }

      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({
          endpoint: 'products',
          method: 'GET',
          params: data
        })
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.list = res.products
        self.loading = false
        self.searching = false
        Vue.set(self, 'page', {
          prev: res.prev ? res.prev : null,
          next: res.next ? res.next : null
        })
      })
      .catch((error) => {
        alert(error)
        self.loading = false
        self.searching = false
      })
    },
    select: function(item) {
      this.$root.viewPrevData = {
        page: this.$parent.page,
        search: this.$parent.search
      }
      this.$root.viewData = item
      this.$root.view = 'PreOrderCreator-edit'
    },
    searchRun: function() {
      this.page = {
        prev: null,
        next: 1
      }
      this.searching = true
      this.getProducts()
    },
    nextProductsPage: function() {
      this.getProducts()
    },
    prevProductsPage: function() {
      this.page.next = this.page.prev
      this.getProducts()
    },
  }
}
</script>