import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import ForgotPassword from '../views/ForgotPassword.vue'
import ResetPassword from '../views/ResetPassword.vue'
import SellerLayout from '../components/layouts/SellerLayout.vue'
import Dashboard from '../views/Dashboard.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard' // Redirect root to dashboard
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true } // Only for non-authenticated users
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true } // Only for non-authenticated users
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPassword,
    meta: { guest: true } // Only for non-authenticated users
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPassword,
    meta: { guest: true } // Only for non-authenticated users
  },
  {
    path: '/',
    component: SellerLayout,
    meta: { requiresAuth: true }, // Requires authentication
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: Dashboard
      },
      {
        path: 'products',
        name: 'Products',
        component: () => import('../views/products/ProductList.vue')
      },
      {
        path: 'products/add',
        name: 'AddProduct',
        component: () => import('../views/products/AddProduct.vue')
      },
      {
        path: 'products/:id/edit',
        name: 'EditProduct',
        component: () => import('../views/products/EditProduct.vue')
      },
      {
        path: 'categories',
        name: 'Categories',
        component: () => import('../views/Categories.vue')
      },
      {
        path: 'orders',
        name: 'Orders',
        component: () => import('../views/Orders.vue')
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('../views/Profile.vue')
      },
      {
        path: 'settings',
        name: 'Settings',
        component: () => import('../views/Settings.vue')
      }
    ]
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Check if user is authenticated
function isAuthenticated() {
  const token = localStorage.getItem('seller_token')
  const user = localStorage.getItem('seller_user')
  return !!(token && user)
}

// Navigation guard
router.beforeEach((to, from, next) => {
  const authenticated = isAuthenticated()
  
  // Check if route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!authenticated) {
      // Not authenticated, redirect to login
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      })
    } else {
      // Authenticated, allow access
      next()
    }
  } 
  // Check if route is for guests only (login/register/forgot-password/reset-password)
  else if (to.matched.some(record => record.meta.guest)) {
    if (authenticated) {
      // Already authenticated, redirect to dashboard
      next('/dashboard')
    } else {
      // Not authenticated, allow access
      next()
    }
  } 
  else {
    // No auth requirements, allow access
    next()
  }
})

export default router