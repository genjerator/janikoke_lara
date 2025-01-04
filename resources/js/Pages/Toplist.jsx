import React from "react";
import Guest from "@/Layouts/GuestLayout.jsx";
import AdminLayout from "@/Layouts/AdminLayout.jsx";

function Toplist(param) {

    const items = param.scores;

    return (
        <>
            <div className="bg-gray-100 flex items-center justify-center">
                <div className="container mx-auto p-4">
                    <header className="mb-6 text-center">
                        <h1 className="text-4xl font-bold text-[#6200EE] sm:text-5xl lg:text-6xl">Top List</h1>
                        <p className="text-[#6200EE] mt-2 text-sm sm:text-base lg:text-lg">Check out the top-ranked
                            items below</p>
                    </header>

                    <div className="bg-white shadow-lg rounded-lg p-6 border-2 border-[#6200EE] sm:p-8 lg:p-10">
                        <ul className="divide-y divide-gray-200">
                            {Object.entries(items).map(([key, value], index) => {
                                const [isOpen, setIsOpen] = React.useState({}); // State to track open items

                                const handleToggle = (index) => {
                                    setIsOpen((prevState) => ({
                                        ...prevState,
                                        [index]: !prevState[index], // Toggle the state for the current item
                                    }));
                                };

                                return (
                                    <li
                                        key={index}
                                        className="flex flex-col py-4 sm:py-6 lg:py-8 border-b"
                                    >
                                        <div className="flex items-center justify-between">
                                            {/* Left Section with Icon and Key */}
                                            <div className="flex items-center gap-2">

                                                {/* Key Name */}
                                                <span className="font-medium text-[#6200EE] text-sm sm:text-base lg:text-lg">
            {index + 1}. {key}
          </span>
                                            </div>

                                            {/* Score */}
                                            <span className="text-[#6200EE] text-sm sm:text-base lg:text-lg">
          Score: {value.total}
        </span>
                                            {/* Plus/Minus Icon */}
                                            <button
                                                onClick={() => handleToggle(index)}
                                                className="text-[#6200EE] hover:text-[#3700B3] focus:outline-none"
                                            >
                                                {isOpen[index] ? (
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        className="h-6 w-6"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        strokeWidth="2"
                                                    >
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M20 12H4" />
                                                    </svg>
                                                ) : (
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        className="h-6 w-6"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        strokeWidth="2"
                                                    >
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                )}
                                            </button>
                                        </div>

                                        {isOpen[index] && (
                                            <div className="mt-2 text-gray-700 text-sm sm:text-base lg:text-lg">
                                                {Object.entries(value.items).map(([key, itemValue], index) => (
                                                    <div key={index} className="flex items-start gap-4">
                                                        <span>{itemValue.created_at} - {itemValue.score_name} ({itemValue.amount})</span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </li>
                                );
                            })}

                        </ul>
                    </div>
                </div>
            </div>
        </>
    );
}

Toplist.layout = page => <Guest children={page}/>
export default Toplist;
