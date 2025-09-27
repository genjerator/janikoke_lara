
import NavLink from "@/Components/NavLink.jsx";

export default function Guest({ children }) {
    return (
        <div className="w-full">
            <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink href={route('admin')} active={route().current('admin')}>
                    Dashboard
                </NavLink>
            </div>

            <div className="w-full ">
                {children}
            </div>
        </div>
    );
}
