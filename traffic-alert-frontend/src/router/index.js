import { createRouter, createWebHistory } from 'vue-router'
import NewsView from '../views/NewsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/news',
      name: 'news',
      component: NewsView,
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('../views/ForgotPasswordView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue'),
    },
    {
      path: '/verify-email',
      name: 'verify-email',
      component: () => import('../views/EmailVerificationView.vue'),
    },
    {
      path: '/alert',
      name: 'alert',
      component: () => import('../views/AlertView.vue'),
    },
    {
      path: '/alert/edit/:id',
      name: 'alert-edit',
      component: () => import('../views/AlertEditView.vue'),
    },
    {
      path: '/alerts',
      name: 'alerts',
      component: () => import('../views/AlertListView.vue'),
    },
    {
      path: '/alerts/:id',
      name: 'alert-detail',
      component: () => import('../views/AlertDetailView.vue'),
    },
    {
      path: '/map',
      name: 'map',
      component: () => import('../views/MapView.vue'),
    },
    {
      path: '/account',
      name: 'account',
      component: () => import('../views/AccountView.vue'),
    },
    {
      path: '/streets/:id',
      name: 'street-detail',
      component: () => import('../views/StreetDetailView.vue'),
    },
    {
      path: '/routes',
      name: 'routes',
      component: () => import('../views/StreetListView.vue'),
    },
    {
      path: '/admin',
      component: () => import('../layouts/AdminLayout.vue'),
      children: [
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('../views/AdminDashboardView.vue'),
        },
        {
          path: 'alerts',
          name: 'admin-alerts',
          component: () => import('../views/AdminAlertListView.vue'),
        },
        {
          path: 'alerts/:id',
          name: 'admin-alert-detail',
          component: () => import('../views/AdminAlertDetailView.vue'),
        },
        {
          path: 'users',
          name: 'admin-users',
          component: () => import('../views/AdminUserListView.vue'),
        },
        {
          path: 'users/:id',
          name: 'admin-user-detail',
          component: () => import('../views/AdminUserDetailView.vue'),
        },
        {
          path: 'events',
          name: 'admin-events',
          component: () => import('../views/AdminEventListView.vue'),
        },
        {
          path: 'events/:id',
          name: 'admin-event-detail',
          component: () => import('../views/AdminEventDetailView.vue'),
        },
        {
          path: '',
          redirect: '/admin/dashboard'
        }
      ]
    },
    {
      path: '/',
      redirect: '/news'
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/news'
    }
  ],
})

export default router
