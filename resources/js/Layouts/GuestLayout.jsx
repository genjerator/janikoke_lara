
import NavLink from "@/Components/NavLink.jsx";

export default function Guest({ children }) {
    return (
        <div className="w-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink href={route('admin')} active={route().current('admin')}>
                    Dashboard
                </NavLink>
            </div>

            <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
