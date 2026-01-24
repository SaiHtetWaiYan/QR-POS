import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global bill alert component for POS pages
Alpine.data('billAlert', () => ({
    showBillAlert: false,
    billAlerts: [],
    showOrderNotification: false,
    orderNotificationMessage: '',

    init() {
        if (typeof Echo !== 'undefined') {
            Echo.private('pos')
                .listen('.BillRequested', (e) => {
                    this.handleBillRequest(e);
                })
                .listen('.OrderPlaced', (e) => {
                    this.handleNewOrder(e);
                });
        }
    },

    async handleBillRequest(data) {
        this.playBillSound();
        const existingIndex = this.billAlerts.findIndex((alert) => alert.order_id === data.order_id);
        if (existingIndex >= 0) {
            this.billAlerts.splice(existingIndex, 1);
        }
        this.billAlerts.unshift(data);
        this.refreshBillAlert();

        const card = document.querySelector(`[data-order-id="${data.order_id}"]`);
        if (!card) return;
        try {
            const response = await fetch(`/pos/orders/${data.order_id}/card`);
            if (!response.ok) return;
            const html = await response.text();
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            const newCard = wrapper.firstElementChild;
            if (!newCard) return;
            card.replaceWith(newCard);
        } catch (error) {
            console.error('Failed to refresh order card:', error);
        }
    },

    handleNewOrder(orderData) {
        this.playOrderSound();
        this.orderNotificationMessage = `New order from ${orderData.table || 'Table'}: ${orderData.order_no}`;
        this.showOrderNotification = true;
        setTimeout(() => {
            this.showOrderNotification = false;
        }, 5000);
    },

    playBillSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
        audio.volume = 0.7;
        audio.play().catch(() => {});
        setTimeout(() => audio.play().catch(() => {}), 300);
    },

    playOrderSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
        audio.volume = 0.5;
        audio.play().catch(() => {});
    },

    refreshBillAlert() {
        if (this.showBillAlert) {
            this.showBillAlert = false;
            this.$nextTick(() => {
                this.showBillAlert = this.billAlerts.length > 0;
            });
        } else {
            this.showBillAlert = this.billAlerts.length > 0;
        }
    },

    dismissBillAlert(orderId = null) {
        if (orderId) {
            const index = this.billAlerts.findIndex((alert) => alert.order_id === orderId);
            if (index >= 0) {
                this.billAlerts.splice(index, 1);
            }
        } else {
            this.billAlerts = [];
        }
        if (this.billAlerts.length === 0) {
            this.showBillAlert = false;
        }
    }
}));

// POS Dashboard component
Alpine.data('posDashboard', (initialPendingCount = 0, initialActiveCount = 0) => ({
    pendingCount: initialPendingCount,
    activeCount: initialActiveCount,
    showNotification: false,
    notificationMessage: '',
    showBillAlert: false,
    billAlerts: [],

    init() {
        if (typeof Echo !== 'undefined') {
            Echo.private('pos')
                .listen('.OrderPlaced', (e) => {
                    this.handleNewOrder(e);
                })
                .listen('.BillRequested', (e) => {
                    this.handleBillRequest(e);
                });
        }
    },

    handleBillRequest(data) {
        this.playBillSound();
        const existingIndex = this.billAlerts.findIndex((alert) => alert.order_id === data.order_id);
        if (existingIndex >= 0) {
            this.billAlerts.splice(existingIndex, 1);
        }
        this.billAlerts.unshift(data);
        this.refreshBillAlert();
    },

    playBillSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
        audio.volume = 0.7;
        audio.play().catch(() => {});
        setTimeout(() => audio.play().catch(() => {}), 300);
    },

    refreshBillAlert() {
        if (this.showBillAlert) {
            this.showBillAlert = false;
            this.$nextTick(() => {
                this.showBillAlert = this.billAlerts.length > 0;
            });
        } else {
            this.showBillAlert = this.billAlerts.length > 0;
        }
    },

    dismissBillAlert(orderId = null) {
        if (orderId) {
            const index = this.billAlerts.findIndex((alert) => alert.order_id === orderId);
            if (index >= 0) {
                this.billAlerts.splice(index, 1);
            }
        } else {
            this.billAlerts = [];
        }
        if (this.billAlerts.length === 0) {
            this.showBillAlert = false;
        }
    },

    async handleNewOrder(orderData) {
        this.playNotificationSound();

        try {
            const response = await fetch(`/pos/orders/${orderData.order_id}/card`);
            if (response.ok) {
                const html = await response.text();
                const pendingContainer = document.getElementById('pending-orders');
                const emptyState = pendingContainer.querySelector('.empty-state');

                if (emptyState) {
                    emptyState.remove();
                }

                const wrapper = document.createElement('div');
                wrapper.innerHTML = html;
                wrapper.firstElementChild.classList.add('animate-slide-in', 'ring-2', 'ring-amber-400', 'ring-offset-2');
                pendingContainer.insertBefore(wrapper.firstElementChild, pendingContainer.firstChild);
                this.pendingCount++;
                this.showToast(orderData);

                setTimeout(() => {
                    const newCard = pendingContainer.firstElementChild;
                    if (newCard) {
                        newCard.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-2');
                    }
                }, 5000);
            }
        } catch (error) {
            console.error('Failed to fetch order card:', error);
            window.location.reload();
        }
    },

    playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
        audio.volume = 0.5;
        audio.play().catch(() => {});
    },

    showToast(orderData) {
        this.notificationMessage = `New order from ${orderData.table || 'Table'}: ${orderData.order_no}`;
        this.showNotification = true;
        setTimeout(() => {
            this.showNotification = false;
        }, 5000);
    }
}));

// Order Card component for status updates
Alpine.data('orderCard', (orderId, updateUrl, csrfToken) => ({
    showPaidConfirm: false,
    loading: false,

    async updateStatus(newStatus) {
        if (this.loading) return;
        this.loading = true;
        try {
            const response = await fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });
            if (!response.ok) throw new Error('Failed to update');
            const data = await response.json();
            if (data.success) {
                await this.handleStatusChange(data.status);
            } else {
                this.loading = false;
            }
        } catch (error) {
            console.error('Failed to update status:', error);
            this.loading = false;
        }
    },

    async handleStatusChange(newStatus) {
        const card = this.$el;
        const orderSelector = `[data-order-id="${orderId}"]`;
        const duplicateCards = document.querySelectorAll(orderSelector);
        duplicateCards.forEach((node) => {
            if (node !== card) node.remove();
        });

        const pendingColumn = document.getElementById('pending-orders');
        const kitchenColumn = document.getElementById('kitchen-orders');
        const wasInPending = card.parentElement === pendingColumn;
        const wasInKitchen = card.parentElement === kitchenColumn;

        // For paid, just fade out and remove
        if (newStatus === 'paid') {
            card.style.transition = 'all 0.3s ease-out';
            card.offsetHeight;
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.remove();
                // Directly decrement kitchen count
                this.adjustCount('kitchen', -1);
            }, 300);
            return;
        }

        // For active statuses, move card to kitchen column and refresh
        if (['accepted', 'preparing', 'served'].includes(newStatus)) {
            if (kitchenColumn) {
                const shouldMove = !wasInKitchen;
                if (shouldMove) {
                    card.style.transition = 'none';
                    card.style.transform = 'translateY(-16px)';
                    card.style.opacity = '0.98';
                    kitchenColumn.insertBefore(card, kitchenColumn.firstChild);
                    card.classList.add('ring-2', 'ring-blue-400', 'ring-offset-2');

                    card.offsetHeight;
                    card.style.transition = 'all 0.25s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';

                    setTimeout(() => {
                        card.classList.remove('ring-2', 'ring-blue-400', 'ring-offset-2');
                    }, 2000);

                    // Directly adjust counts
                    if (wasInPending) {
                        this.adjustCount('pending', -1);
                    }
                    this.adjustCount('kitchen', 1);
                }

                await this.refreshCardContent(card, true);
                return;
            }
        }

        // For other status changes, fetch and replace card content
        await this.refreshCardContent(card, true);
    },

    adjustCount(type, delta) {
        const dashboard = document.querySelector('[x-data*="posDashboard"]');
        if (dashboard && dashboard._x_dataStack && dashboard._x_dataStack[0]) {
            if (type === 'pending') {
                dashboard._x_dataStack[0].pendingCount = Math.max(0, dashboard._x_dataStack[0].pendingCount + delta);
            } else if (type === 'kitchen') {
                dashboard._x_dataStack[0].activeCount = Math.max(0, dashboard._x_dataStack[0].activeCount + delta);
            }
        }
        this.updateEmptyStates();
    },

    updateEmptyStates() {
        const pendingContainer = document.getElementById('pending-orders');
        const kitchenContainer = document.getElementById('kitchen-orders');
        const dashboard = document.querySelector('[x-data*="posDashboard"]');

        if (!dashboard || !dashboard._x_dataStack) return;

        const pendingCount = dashboard._x_dataStack[0].pendingCount;
        const activeCount = dashboard._x_dataStack[0].activeCount;

        const pendingEmpty = pendingContainer ? pendingContainer.querySelector('.empty-state') : null;
        if (pendingEmpty) {
            pendingEmpty.style.display = pendingCount > 0 ? 'none' : '';
        }

        const kitchenEmpty = kitchenContainer ? kitchenContainer.querySelector('.empty-state') : null;
        if (kitchenEmpty) {
            kitchenEmpty.style.display = activeCount > 0 ? 'none' : '';
        }
    },

    async refreshCardContent(card, animate = false) {
        try {
            const response = await fetch('/pos/orders/' + orderId + '/card');
            if (!response.ok) return;
            const html = await response.text();
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const newCard = wrapper.firstElementChild;
            if (animate) {
                newCard.style.opacity = '0';
                newCard.style.transform = 'translateY(-6px)';
            }
            card.replaceWith(newCard);
            Alpine.initTree(newCard);
            if (animate) {
                requestAnimationFrame(() => {
                    newCard.style.transition = 'all 0.2s ease-out';
                    newCard.style.opacity = '1';
                    newCard.style.transform = 'translateY(0)';
                });
            }
        } catch (error) {
            console.error('Failed to refresh card:', error);
        }
    },

    updateCounts() {
        const pendingContainer = document.getElementById('pending-orders');
        const pendingCount = pendingContainer ? pendingContainer.querySelectorAll('[data-order-id]').length : 0;
        const kitchenContainer = document.getElementById('kitchen-orders');
        const activeCount = kitchenContainer ? kitchenContainer.querySelectorAll('[data-order-id]').length : 0;
        const dashboard = document.querySelector('[x-data*="posDashboard"]');
        if (dashboard && dashboard._x_dataStack && dashboard._x_dataStack[0]) {
            dashboard._x_dataStack[0].pendingCount = pendingCount;
            dashboard._x_dataStack[0].activeCount = activeCount;
        }

        // Show/hide empty states
        const pendingEmpty = pendingContainer ? pendingContainer.querySelector('.empty-state') : null;
        if (pendingEmpty) {
            if (pendingCount > 0) {
                pendingEmpty.style.display = 'none';
            } else {
                pendingEmpty.style.display = '';
            }
        }

        const kitchenEmpty = kitchenContainer ? kitchenContainer.querySelector('.empty-state') : null;
        if (kitchenEmpty) {
            if (activeCount > 0) {
                kitchenEmpty.style.display = 'none';
            } else {
                kitchenEmpty.style.display = '';
            }
        }
    },

    submitPaid() {
        this.showPaidConfirm = false;
        this.updateStatus('paid');
    }
}));

Alpine.start();
