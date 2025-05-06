<template>
  <nav class="navbar navbar-expand-lg shadow-lg" style="background: linear-gradient(to right, #007bff, #0056b3);">
      <div class="container-fluid px-4">
          <a class="navbar-brand text-white fw-bold d-flex align-items-center" :href="homeUrl">
              <i class="fas fa-utensils me-2"></i> foodEASY
          </a>
          <button class="navbar-toggler border-0" type="button" @click="toggleMenu"
              aria-controls="navbarNav" :aria-expanded="isMenuOpen" aria-label="Prepnúť navigáciu">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" :class="{ 'show': isMenuOpen }" id="navbarNav">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                      <a class="nav-link text-white px-3 py-2" :class="{ 'active': isActive('recipes.index') }"
                          :href="getRoute('recipes.index')">
                          <i class="fas fa-book-open me-1"></i> Recepty
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link text-white px-3 py-2" :class="{ 'active': isActive('plans.index') }"
                          :href="getRoute('plans.index')">
                          <i class="fas fa-calendar-alt me-1"></i> Stravovacie plány
                      </a>
                  </li>
              </ul>
              <div class="d-flex align-items-center">
                  <div v-if="isAuthenticated">
                      <div class="dropdown">
                          <a class="nav-link text-white dropdown-toggle d-flex align-items-center"
                              @click="toggleDropdown" role="button">
                              <i class="fas fa-user-circle me-1"></i> {{ userName }}
                          </a>
                          <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                              v-show="isDropdownOpen">
                              <li>
                                  <a class="dropdown-item" :href="getRoute('profile.show')">
                                      <i class="fas fa-user me-2"></i> Profil
                                  </a>
                              </li>
                              <li><hr class="dropdown-divider"></li>
                              <li>
                                  <form :action="getRoute('logout')" method="POST" @submit.prevent="logout">
                                      <input type="hidden" name="_token" :value="csrfToken">
                                      <button type="submit" class="dropdown-item">
                                          <i class="fas fa-sign-out-alt me-2"></i> Odhlásiť sa
                                      </button>
                                  </form>
                              </li>
                          </ul>
                      </div>
                  </div>
                  <div v-else class="d-flex align-items-center">
                      <a class="btn btn-outline-light me-2 px-4 py-2" :href="getRoute('login')">
                          <i class="fas fa-sign-in-alt me-1"></i> Prihlásiť sa
                      </a>
                      <a class="btn btn-light px-4 py-2" :href="getRoute('register')">
                          <i class="fas fa-user-plus me-1"></i> Zaregistrovať sa
                      </a>
                  </div>
              </div>
          </div>
      </div>
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  props: {
      isAuthenticated: {
          type: Boolean,
          default: false
      },
      userName: {
          type: String,
          default: ''
      },
      csrfToken: {
          type: String,
          default: ''
      },
      homeUrl: {
          type: String,
          default: '/'
      },
      routes: {
          type: Object,
          default: () => ({})
      }
  },
  data() {
      return {
          isMenuOpen: false,
          isDropdownOpen: false
      };
  },
  methods: {
      toggleMenu() {
          this.isMenuOpen = !this.isMenuOpen;
      },
      toggleDropdown() {
          this.isDropdownOpen = !this.isDropdownOpen;
      },
      getRoute(name) {
          return this.routes[name] || '#';
      },
      isActive(routeName) {
          return window.location.pathname === this.getRoute(routeName);
      },
      logout(event) {
          event.target.submit();
      }
  }
};
</script>

<style scoped>
.navbar {
  padding: 1.2rem 0;
}

.navbar-brand {
  font-size: 1.8rem;
  color: #f8f9fa !important;
  transition: transform 0.3s ease, color 0.3s ease;
}

.navbar-brand:hover {
  transform: scale(1.05);
  color: #ffffff !important;
}

.nav-link {
  font-weight: 500;
  color: #f8f9fa !important;
  border-radius: 0.5rem;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #ffffff !important;
}

.nav-link.active {
  background-color: rgba(255, 255, 255, 0.2);
  color: #ffffff !important;
}

.btn-outline-light, .btn-light {
  border-radius: 0.5rem;
  font-weight: 500;
  padding: 0.5rem 1.5rem;
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.btn-outline-light:hover {
  transform: translateY(-2px);
  background-color: #f8f9fa;
  color: #007bff;
}

.btn-light:hover {
  transform: translateY(-2px);
  background-color: #e9ecef;
  color: #007bff;
}

.dropdown-menu {
  border-radius: 0.75rem;
  background-color: #fff;
  padding: 0.5rem 0;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  padding: 0.5rem 1.5rem;
  transition: background-color 0.3s ease;
}

.dropdown-item:hover {
  background-color: #f1f3f5;
}

.navbar-toggler {
  transition: transform 0.3s ease;
}

.navbar-toggler:hover {
  transform: scale(1.1);
}

.navbar-collapse {
  transition: all 0.3s ease;
}
</style>