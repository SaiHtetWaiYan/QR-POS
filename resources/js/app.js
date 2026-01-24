import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global bill alert component for POS pages
Alpine.data('billAlert', () => ({
    showBillAlert: false,
    billAlertData: null,

    init() {
        if (typeof Echo !== 'undefined') {
            Echo.private('pos')
                .listen('.BillRequested', (e) => {
                    this.handleBillRequest(e);
                });
        }
    },

    handleBillRequest(data) {
        this.playBillSound();
        this.billAlertData = data;
        if (this.showBillAlert) {
            this.showBillAlert = false;
            this.$nextTick(() => {
                this.showBillAlert = true;
            });
        } else {
            this.showBillAlert = true;
        }
    },

    playBillSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
        audio.volume = 0.7;
        audio.play().catch(() => {});
        setTimeout(() => audio.play().catch(() => {}), 300);
    },

    dismissBillAlert() {
        this.showBillAlert = false;
        this.billAlertData = null;
    }
}));

// POS Dashboard component
Alpine.data('posDashboard', (initialPendingCount = 0) => ({
    pendingCount: initialPendingCount,
    showNotification: false,
    notificationMessage: '',
    showBillAlert: false,
    billAlertData: null,

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
        this.billAlertData = data;
        if (this.showBillAlert) {
            this.showBillAlert = false;
            this.$nextTick(() => {
                this.showBillAlert = true;
            });
        } else {
            this.showBillAlert = true;
        }
    },

    playBillSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZSQiHlsYF1le4mVm5eOf2xeVlxsg5OdnpaCbVlLTF55kZ6hmoRqUkE+T2+Lnqahk3pbRTc6VHSQoqWfj3RWQDA2VHKRpKehk3NZQC81VXWUpqijlHZcQzE4WXmYqaull3pgRTU8X3+crqupmH1kSD1BY4OgsK2snYJpS0JGaIejtK+tn4ZtT0dKboumuLGwoIlxVEtOcY+qurSyo41zV09TdpOtvbW0pJB3XFNYe5iwv7e2ppR8YFhdgJy0wLi4qJd/ZV1jhaC3wb27qpqDaWFniqS6w7+9rZyGbmVrjqi+xMHAr5+KcmlvkqzBxsLCsaKMdm1zmq/Dx8TEtKWPeXB3n7PGyMbGt6iSfHR7o7fIysrJuquVgHh/qLvLzMvLvrCYhH2DrL/NzdHOwrSdh4GGsMLP0NLRxLahioSJtMXS09TUx7mjjYiNuMnV1tfXyr2mj4yRu8vX2NnZzL+okY+UvtDa29vc0MGql5OYwtPc3d/e08Ssm5ecxdXf4ODh1cevnpugyd7h4uPi18qyoZ+jzuDj5OXl2sy0pKKm0uPl5ufn3c+2p6aq1eXn6Onp4NLAqqiu2Ojq6+vr49TBra2y2+vt7u7t5tXDsLC13O3v8PHw6NfFtLS54O/x8vLy6tnHt7e74/Hz9PT07NvJuru/5vP19vb17t3Lvr7C6fT29/f38N/MwMHF7PX3+Pj58eHOw8TI7vf4+fn5+OLQxcfL8Pj5+vr6+eTSx8nO8fn6+/v7++XUycrQ8/r7/Pz8/OfWy8zT9Pv8/f39/unYzs/W9vz9/v7+/+vaz9HZ9/3+////////7NzR0tz4/v////////7u3tPU3vn///////////zv4NXX4Pr////////////w4dfZ4vv////////////y49na5fz////////////05Nzc6P3////////////15t7e6v7////////////26N/g7P/////////////36uLi7v/////////////47OTk8P/////////////57ubm8v/////////////77+jo9P/////////////88Orq9v/////////////98uzs+P/////////////+9O7u+v////////////8=');
        audio.volume = 0.7;
        audio.play().catch(() => {});
        setTimeout(() => audio.play().catch(() => {}), 300);
    },

    dismissBillAlert() {
        this.showBillAlert = false;
        this.billAlertData = null;
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

Alpine.start();
