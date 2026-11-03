export default function InsightStatusBadge({
    isPublished,
}: {
    isPublished: boolean;
}) {
    return isPublished ? (
        <span className="rounded-full bg-mustard-100 px-2.5 py-0.5 text-xs font-medium text-mustard-800">
            Published
        </span>
    ) : (
        <span className="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-medium text-stone-600">
            Draft
        </span>
    );
}
