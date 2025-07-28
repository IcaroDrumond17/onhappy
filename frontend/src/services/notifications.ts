import http from './http';

export async function fetchNotifications() {
  const response = await http.get('/notifications');
  return response.data;
}

export async function markNotificationViewed(id: number) {
  const response = await http.patch(`/notifications/${id}/viewed`);
  return response.data;
}
