import { Head, router } from "@inertiajs/react";

export default function NotificationsIndex({ notifications }: any) {
    return (
        <>
            <Head title="Notifications" />

            <div className="max-w-5xl p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Notifications</h1>

                    <button
                        onClick={() => router.patch("/notifications/read-all")}
                        className="rounded border px-3 py-2"
                    >
                        Mark all as read
                    </button>
                </div>

                <div className="space-y-3">
                    {notifications.data.map((notification: any) => (
                        <div
                            key={notification.id}
                            className={`rounded border p-4 ${
                                notification.read_at ? "" : "bg-muted/40"
                            }`}
                        >
                            <h2 className="font-medium">
                                {notification.data.title}
                            </h2>

                            <p className="mt-1 text-sm">
                                {notification.data.message}
                            </p>

                            {!notification.read_at && (
                                <button
                                    className="mt-3 text-sm underline"
                                    onClick={() =>
                                        router.patch(
                                            `/notifications/${notification.id}/read`,
                                        )
                                    }
                                >
                                    Mark as read
                                </button>
                            )}
                        </div>
                    ))}

                    {notifications.data.length === 0 && (
                        <div className="rounded border p-6">
                            No notifications.
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
