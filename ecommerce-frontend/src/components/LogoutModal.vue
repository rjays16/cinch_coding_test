<template>
  <transition name="modal-fade">
    <div v-if="show" class="modal-overlay" @click="closeModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <div class="modal-icon">
            <i class="fas fa-sign-out-alt"></i>
          </div>
          <h3>Confirm Logout</h3>
        </div>

        <div class="modal-body">
          <p>Are you sure you want to logout?</p>
        </div>

        <div class="modal-footer">
          <button 
            class="btn-cancel" 
            @click="closeModal"
            :disabled="isLoggingOut"
          >
            Cancel
          </button>
          <button 
            class="btn-confirm" 
            @click="confirmLogout"
            :disabled="isLoggingOut"
          >
            <span v-if="!isLoggingOut">Logout</span>
            <span v-else>
              <i class="fas fa-spinner fa-spin"></i> Logging out...
            </span>
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'LogoutModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    isLoggingOut: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'confirm'],
  methods: {
    closeModal() {
      if (!this.isLoggingOut) {
        this.$emit('close')
      }
    },
    confirmLogout() {
      if (!this.isLoggingOut) {
        this.$emit('confirm')
      }
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.modal-container {
  background: white;
  border-radius: 12px;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    transform: translateY(-50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  padding: 30px 30px 20px 30px;
  text-align: center;
}

.modal-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #fff3cd;
  color: #856404;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  font-size: 32px;
}

.modal-header h3 {
  margin: 0;
  font-size: 22px;
  font-weight: 600;
  color: #2c3e50;
}

.modal-body {
  padding: 0 30px 30px 30px;
  text-align: center;
}

.modal-body p {
  margin: 0;
  font-size: 15px;
  line-height: 1.6;
  color: #666;
}

.modal-footer {
  padding: 20px 30px 30px 30px;
  display: flex;
  gap: 12px;
  justify-content: center;
}

.btn-cancel,
.btn-confirm {
  padding: 12px 32px;
  border: none;
  border-radius: 6px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  min-width: 100px;
}

.btn-cancel {
  background: #f5f5f5;
  color: #666;
}

.btn-cancel:hover:not(:disabled) {
  background: #e0e0e0;
}

.btn-confirm {
  background: #ff4444;
  color: white;
}

.btn-confirm:hover:not(:disabled) {
  background: #cc0000;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(255, 68, 68, 0.3);
}

/* Disabled state */
.btn-cancel:disabled,
.btn-confirm:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

.btn-cancel:disabled {
  background: #f5f5f5;
}

.btn-confirm:disabled {
  background: #ff4444;
}

/* Spinner animation */
.fa-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Transition */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .modal-container {
    max-width: 90%;
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding-left: 20px;
    padding-right: 20px;
  }

  .modal-footer {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-confirm {
    width: 100%;
  }
}
</style>