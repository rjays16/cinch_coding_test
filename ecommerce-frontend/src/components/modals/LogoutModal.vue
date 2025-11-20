<template>
  <div v-if="show" class="modal-overlay" @click="handleCancel">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <i class="fas fa-sign-out-alt modal-icon"></i>
        <h3>Confirm Logout</h3>
      </div>
      
      <div class="modal-body">
        <p>Are you sure you want to logout?</p>
      </div>
      
      <div class="modal-footer">
        <button 
          @click="handleCancel" 
          class="btn-cancel"
          :disabled="isLoading"
        >
          Cancel
        </button>
        <button 
          @click="handleConfirm" 
          class="btn-confirm"
          :disabled="isLoading"
        >
          <span v-if="!isLoading">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </span>
          <span v-else class="loading-content">
            <i class="fas fa-spinner fa-spin"></i>
            Logging out...
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LogoutModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    isLoading: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    handleCancel() {
      if (!this.isLoading) {
        this.$emit('cancel')
      }
    },
    handleConfirm() {
      if (!this.isLoading) {
        this.$emit('confirm')
      }
    }
  },
  mounted() {
    console.log('✅ LogoutModal mounted')
  },
  watch: {
    show(newVal) {
      console.log('🔔 Modal show changed:', newVal)
    }
  }
}
</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  padding: 1rem;
}

/* Modal Content */
.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Modal Header */
.modal-header {
  padding: 1.5rem;
  text-align: center;
  border-bottom: 1px solid #f0f0f0;
}

.modal-icon {
  width: 60px;
  height: 60px;
  background: #fff3cd;
  color: #ff9800;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  margin-bottom: 1rem;
}

.modal-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

/* Modal Body */
.modal-body {
  padding: 1.5rem;
  text-align: center;
}

.modal-body p {
  margin: 0;
  color: #666;
  font-size: 1rem;
  line-height: 1.6;
}

/* Modal Footer */
.modal-footer {
  padding: 1rem 1.5rem 1.5rem;
  display: flex;
  gap: 1rem;
}

/* Buttons */
.btn-cancel,
.btn-confirm {
  flex: 1;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-cancel {
  background: #f0f0f0;
  color: #666;
}

.btn-cancel:hover:not(:disabled) {
  background: #e0e0e0;
}

.btn-confirm {
  background: #e74c3c;
  color: white;
}

.btn-confirm:hover:not(:disabled) {
  background: #c0392b;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.btn-cancel:disabled,
.btn-confirm:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Loading Content */
.loading-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.loading-content i {
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

/* Responsive */
@media (max-width: 768px) {
  .modal-content {
    max-width: 90%;
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