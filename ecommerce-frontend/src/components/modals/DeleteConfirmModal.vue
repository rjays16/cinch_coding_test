<template>
  <div v-if="show" class="modal-overlay" @click="handleCancel">
    <div class="modal-content" @click.stop>
      <div class="modal-icon">
        <i class="fas fa-trash-alt"></i>
      </div>
      
      <h2>Remove Item?</h2>
      
      <p class="modal-message">
        Are you sure you want to remove <strong>{{ itemName }}</strong> from your cart?
      </p>

      <div class="modal-actions">
        <button 
          @click="handleCancel" 
          class="btn-cancel"
          :disabled="isProcessing"
        >
          <i class="fas fa-times"></i>
          Cancel
        </button>
        <button 
          @click="handleConfirm" 
          class="btn-confirm"
          :disabled="isProcessing"
        >
          <span v-if="!isProcessing">
            <i class="fas fa-trash-alt"></i>
            Remove
          </span>
          <span v-else>
            <i class="fas fa-spinner fa-spin"></i>
            Removing...
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DeleteConfirmModal',
  props: {
    show: {
      type: Boolean,
      required: true
    },
    itemName: {
      type: String,
      default: 'this item'
    },
    isProcessing: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    handleConfirm() {
      if (!this.isProcessing) {
        this.$emit('confirm')
      }
    },
    handleCancel() {
      if (!this.isProcessing) {
        this.$emit('cancel')
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
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  max-width: 450px;
  width: 100%;
  padding: 2.5rem 2rem;
  text-align: center;
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

.modal-icon {
  width: 80px;
  height: 80px;
  background: #ffebee;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.modal-icon i {
  font-size: 2.5rem;
  color: #e74c3c;
}

.modal-content h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 1.75rem;
}

.modal-message {
  margin: 0 0 2rem 0;
  color: #666;
  line-height: 1.6;
  font-size: 1rem;
}

.modal-message strong {
  color: #2c3e50;
  font-weight: 600;
}

.modal-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.btn-cancel,
.btn-confirm {
  padding: 1rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-size: 1rem;
}

.btn-cancel {
  background: white;
  color: #666;
  border: 2px solid #e0e0e0;
}

.btn-cancel:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #ccc;
}

.btn-cancel:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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

.btn-confirm:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-confirm span,
.btn-cancel span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 2rem 1.5rem;
  }

  .modal-content h2 {
    font-size: 1.5rem;
  }

  .modal-actions {
    grid-template-columns: 1fr;
  }
}
</style>